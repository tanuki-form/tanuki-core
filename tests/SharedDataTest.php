<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\SharedData;

class SharedDataTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $data = new SharedData();
        $data->set('key', 'value');

        $this->assertSame('value', $data->get('key'));
    }

    public function testGetReturnsNullForUnknownKey(): void
    {
        $data = new SharedData();
        $this->assertNull($data->get('unknown'));
    }

    public function testOverwriteValue(): void
    {
        $data = new SharedData();
        $data->set('key', 'first');
        $data->set('key', 'second');

        $this->assertSame('second', $data->get('key'));
    }
}
