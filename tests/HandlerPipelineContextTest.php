<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\SharedData;

class HandlerPipelineContextTest extends TestCase
{
    public function testInitiallyNoError(): void
    {
        $ctx = new HandlerPipelineContext();
        $this->assertFalse($ctx->hasError());
        $this->assertEmpty($ctx->getResults());
    }

    public function testAddSuccessResultNoError(): void
    {
        $ctx = new HandlerPipelineContext();
        $ctx->addResult(HandlerResult::success('Test'));

        $this->assertFalse($ctx->hasError());
        $this->assertCount(1, $ctx->getResults());
    }

    public function testAddFailureResultSetsError(): void
    {
        $ctx = new HandlerPipelineContext();
        $ctx->addResult(HandlerResult::failure('Test', 'error'));

        $this->assertTrue($ctx->hasError());
    }

    public function testSharedDataGetSet(): void
    {
        $ctx = new HandlerPipelineContext();
        $ctx->set('key', 'value');

        $this->assertSame('value', $ctx->get('key'));
    }

    public function testSharedDataReturnsNullForUnknownKey(): void
    {
        $ctx = new HandlerPipelineContext();
        $this->assertNull($ctx->get('nonexistent'));
    }

    public function testSharedDataIsSharedBetweenContexts(): void
    {
        $shared = new SharedData();
        $ctx1 = new HandlerPipelineContext($shared);
        $ctx2 = new HandlerPipelineContext($shared);

        $ctx1->set('token', '123');
        $this->assertSame('123', $ctx2->get('token'));
    }
}
