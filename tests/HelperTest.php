<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\Helper;

class HelperTest extends TestCase
{
    public function testRegisterAndCall(): void
    {
        $helper = new Helper();
        $helper->register('greet', fn(string $name) => "Hello, {$name}!");

        $this->assertSame('Hello, Taro!', $helper->greet('Taro'));
    }

    public function testCallWithNoArguments(): void
    {
        $helper = new Helper();
        $helper->register('getToken', fn() => 'abc123');

        $this->assertSame('abc123', $helper->getToken());
    }

    public function testCallThrowsForUnregisteredHelper(): void
    {
        $helper = new Helper();

        $this->expectException(\BadMethodCallException::class);
        $helper->nonexistent();
    }
}
