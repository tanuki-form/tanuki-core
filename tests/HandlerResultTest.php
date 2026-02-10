<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\HandlerResult;

class HandlerResultTest extends TestCase
{
    public function testSuccess(): void
    {
        $result = HandlerResult::success('TestHandler', ['key' => 'value']);

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->isFailure());
        $this->assertFalse($result->wasSkipped());
        $this->assertSame('TestHandler', $result->getIdentifier());
        $this->assertSame(['key' => 'value'], $result->getData());
    }

    public function testFailure(): void
    {
        $result = HandlerResult::failure('TestHandler', 'something went wrong', ['detail' => 'x']);

        $this->assertFalse($result->isSuccessful());
        $this->assertTrue($result->isFailure());
        $this->assertFalse($result->wasSkipped());
        $this->assertSame('something went wrong', $result->getErrorMessage());
        $this->assertSame(['detail' => 'x'], $result->getData());
    }

    public function testSkipped(): void
    {
        $result = HandlerResult::skipped('TestHandler');

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->isFailure());
        $this->assertTrue($result->wasSkipped());
    }

    public function testSuccessHasNullErrorMessage(): void
    {
        $result = HandlerResult::success('TestHandler');
        $this->assertNull($result->getErrorMessage());
    }

    public function testJsonSerialize(): void
    {
        $result = HandlerResult::success('TestHandler');
        $json = $result->jsonSerialize();

        $this->assertSame('TestHandler', $json['identifier']);
        $this->assertTrue($json['isSuccessful']);
        $this->assertFalse($json['wasSkipped']);
        $this->assertNull($json['errorMessage']);
    }
}
