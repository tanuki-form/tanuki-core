<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\FormSchema;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\NormalizerRegistry;
use Tanuki\Validator;
use Tanuki\Factory\FieldFactory;

class AbstractHandlerTest extends TestCase
{
    private function createForm(): Form
    {
        $schema = FormSchema::fromArray([], new FieldFactory());
        return new Form($schema, new Validator(), new NormalizerRegistry());
    }

    public function testDefaultHandleReturnsSuccess(): void
    {
        $handler = new class extends AbstractHandler {};

        $form = $this->createForm();
        $ctx = new HandlerPipelineContext();
        $result = $handler->handle($form, $ctx);

        $this->assertTrue($result->isSuccessful());
    }

    public function test__handleCallsConfiguredAction(): void
    {
        $handler = new class([], 'customAction') extends AbstractHandler {
            public function customAction(Form $form, HandlerPipelineContext $ctx): HandlerResult
            {
                return $this->success(['custom' => true]);
            }
        };

        $form = $this->createForm();
        $ctx = new HandlerPipelineContext();
        $result = $handler->__handle($form, $ctx);

        $this->assertTrue($result->isSuccessful());
        $this->assertSame(['custom' => true], $result->getData());
    }

    public function test__handleThrowsForMissingMethod(): void
    {
        $handler = new class([], 'nonexistentMethod') extends AbstractHandler {};

        $form = $this->createForm();
        $ctx = new HandlerPipelineContext();

        $this->expectException(\RuntimeException::class);
        $handler->__handle($form, $ctx);
    }
}
