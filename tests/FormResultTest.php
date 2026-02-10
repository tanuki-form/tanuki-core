<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\Form;
use Tanuki\FormResult;
use Tanuki\FormSchema;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\NormalizerRegistry;
use Tanuki\Validator;
use Tanuki\Factory\FieldFactory;

class FormResultTest extends TestCase
{
    private function createForm(array $schemaDef = []): Form
    {
        $schema = FormSchema::fromArray($schemaDef, new FieldFactory());
        $registry = new NormalizerRegistry();
        $registry->register('strval', 'strval');
        return new Form($schema, new Validator(), $registry);
    }

    public function testSuccessWhenNoErrors(): void
    {
        $form = $this->createForm();
        $form->bind([]);

        $preCtx = new HandlerPipelineContext();
        $postCtx = new HandlerPipelineContext();
        $preCtx->addResult(HandlerResult::success('Pre'));
        $postCtx->addResult(HandlerResult::success('Post'));

        $result = FormResult::fromContextAndForm($preCtx, $postCtx, $form);

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->hasValidationErrors());
        $this->assertCount(1, $result->getPreHandlerResults());
        $this->assertCount(1, $result->getPostHandlerResults());
    }

    public function testFailureOnPreHandlerError(): void
    {
        $form = $this->createForm();
        $form->bind([]);

        $preCtx = new HandlerPipelineContext();
        $postCtx = new HandlerPipelineContext();
        $preCtx->addResult(HandlerResult::failure('Pre', 'error'));

        $result = FormResult::fromContextAndForm($preCtx, $postCtx, $form);

        $this->assertFalse($result->isSuccessful());
    }

    public function testFailureOnValidationErrors(): void
    {
        $form = $this->createForm([
            'name' => [
                'type' => 'value',
                'validation' => ['required' => true],
            ],
        ]);
        $form->bind(['name' => '']);
        $form->validate();

        $preCtx = new HandlerPipelineContext();
        $postCtx = new HandlerPipelineContext();

        $result = FormResult::fromContextAndForm($preCtx, $postCtx, $form);

        $this->assertFalse($result->isSuccessful());
        $this->assertTrue($result->hasValidationErrors());
        $this->assertArrayHasKey('name', $result->getValidationErrors());
    }

    public function testFailureOnPostHandlerError(): void
    {
        $form = $this->createForm();
        $form->bind([]);

        $preCtx = new HandlerPipelineContext();
        $postCtx = new HandlerPipelineContext();
        $postCtx->addResult(HandlerResult::failure('Post', 'write failed'));

        $result = FormResult::fromContextAndForm($preCtx, $postCtx, $form);

        $this->assertFalse($result->isSuccessful());
    }
}
