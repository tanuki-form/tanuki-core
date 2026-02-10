<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\FormSchema;
use Tanuki\HandlerPipeline;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\Helper;
use Tanuki\NormalizerRegistry;
use Tanuki\Validator;
use Tanuki\Factory\FieldFactory;

class HandlerPipelineTest extends TestCase
{
    private function createForm(): Form
    {
        $schema = FormSchema::fromArray([], new FieldFactory());
        return new Form($schema, new Validator(), new NormalizerRegistry());
    }

    public function testExecuteRunsAllHandlers(): void
    {
        $pipeline = new HandlerPipeline('post');
        $pipeline->addHandler(new class extends AbstractHandler {});
        $pipeline->addHandler(new class extends AbstractHandler {});

        $form = $this->createForm();
        $ctx = new HandlerPipelineContext();
        $pipeline->execute($form, $ctx);

        $this->assertCount(2, $ctx->getResults());
        $this->assertFalse($ctx->hasError());
    }

    public function testExecuteCollectsFailures(): void
    {
        $pipeline = new HandlerPipeline('pre');
        $pipeline->addHandler(new class extends AbstractHandler {
            public function handle(Form $form, HandlerPipelineContext $ctx): HandlerResult {
                return $this->failure('test-error');
            }
        });

        $form = $this->createForm();
        $ctx = new HandlerPipelineContext();
        $pipeline->execute($form, $ctx);

        $this->assertTrue($ctx->hasError());
        $this->assertTrue($ctx->getResults()[0]->isFailure());
    }
}
