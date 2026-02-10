<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\FormSchema;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\Validator;
use Tanuki\NormalizerRegistry;
use Tanuki\Factory\FieldFactory;

class FormTest extends TestCase
{
    private function createForm(array $schemaDef): Form
    {
        $factory = new FieldFactory();
        $schema = FormSchema::fromArray($schemaDef, $factory);
        $validator = new Validator();
        $registry = new NormalizerRegistry();
        $registry->register('strval', 'strval');

        return new Form($schema, $validator, $registry);
    }

    // --- bind & getRawData ---

    public function testBindExtractsSchemaFieldsOnly(): void
    {
        $form = $this->createForm([
            'name' => ['type' => 'value'],
            'email' => ['type' => 'value'],
        ]);

        $form->bind([
            'name' => 'Taro',
            'email' => 'taro@example.com',
            'extra' => 'should be ignored',
        ]);

        $raw = $form->getRawData();
        $this->assertSame('Taro', $raw['name']);
        $this->assertSame('taro@example.com', $raw['email']);
        $this->assertArrayNotHasKey('extra', $raw);
    }

    public function testBindSetsNullForMissingFields(): void
    {
        $form = $this->createForm([
            'name' => ['type' => 'value'],
        ]);

        $form->bind([]);
        $this->assertNull($form->getRawData()['name']);
    }

    // --- getNormalizedData ---

    public function testGetNormalizedData(): void
    {
        $form = $this->createForm([
            'age' => ['type' => 'value'],
        ]);

        $form->bind(['age' => 25]);
        $normalized = $form->getNormalizedData();

        $this->assertSame('25', $normalized['age']);
    }

    // --- validate ---

    public function testValidatePassesWithValidData(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->bind(['name' => 'Taro']);
        $this->assertTrue($form->validate());
        $this->assertFalse($form->hasValidationErrors());
    }

    public function testValidateFailsWithInvalidData(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->bind(['name' => '']);
        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasValidationErrors());

        $errors = $form->getValidationErrors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertContains('required', $errors['name']);
    }

    public function testValidateAccumulatesMultipleErrors(): void
    {
        $form = $this->createForm([
            'email' => [
                'type' => 'value',
                'validation' => [
                    'required' => true,
                    'email' => true,
                ],
            ],
        ]);

        $form->bind(['email' => '']);
        $form->validate();

        $errors = $form->getValidationErrors();
        $this->assertCount(2, $errors['email']);
        $this->assertContains('required', $errors['email']);
        $this->assertContains('email', $errors['email']);
    }

    public function testValidateMultipleFieldsAccumulatesSeparately(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
            'email' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->bind(['name' => '', 'email' => '']);
        $form->validate();

        $errors = $form->getValidationErrors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    // --- send() integration ---

    public function testSendSucceedsWithValidData(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->bind(['name' => 'Taro']);
        $result = $form->send();

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->hasValidationErrors());
    }

    public function testSendFailsOnValidationError(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->bind(['name' => '']);
        $result = $form->send();

        $this->assertFalse($result->isSuccessful());
        $this->assertTrue($result->hasValidationErrors());
    }

    public function testSendSkipsValidationWhenPreHandlerFails(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->addPreHandler(new class extends AbstractHandler {
            public function handle(Form $form, HandlerPipelineContext $ctx): HandlerResult
            {
                return $this->failure('blocked');
            }
        });

        $form->bind(['name' => 'Taro']);
        $result = $form->send();

        $this->assertFalse($result->isSuccessful());
        $this->assertFalse($result->hasValidationErrors());
        $this->assertCount(1, $result->getPreHandlerResults());
    }

    public function testSendSkipsPostHandlersOnValidationError(): void
    {
        $executed = false;

        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->addPostHandler(new class($executed) extends AbstractHandler {
            private bool $flag;
            public function __construct(bool &$flag) { $this->flag = &$flag; parent::__construct(); }
            public function handle(Form $form, HandlerPipelineContext $ctx): HandlerResult
            {
                $this->flag = true;
                return $this->success();
            }
        });

        $form->bind(['name' => '']);
        $form->send();

        $this->assertFalse($executed);
    }

    public function testSendExecutesPostHandlersOnSuccess(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);

        $form->addPostHandler(new class extends AbstractHandler {
            public function handle(Form $form, HandlerPipelineContext $ctx): HandlerResult
            {
                return $this->success(['processed' => true]);
            }
        });

        $form->bind(['name' => 'Taro']);
        $result = $form->send();

        $this->assertTrue($result->isSuccessful());
        $this->assertCount(1, $result->getPostHandlerResults());
    }

    // --- addHelper ---

    public function testAddHelperRegistersCallable(): void
    {
        $form = $this->createForm([]);
        $form->addHelper('greet', fn() => 'hello');

        $this->assertSame('hello', $form->helper->greet());
    }
}
