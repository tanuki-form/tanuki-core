<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\NormalizerRegistry;

class NormalizerRegistryTest extends TestCase
{
    private NormalizerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new NormalizerRegistry();
    }

    public function testRegisterAndResolve(): void
    {
        $normalizer = fn($v) => strtoupper($v);
        $this->registry->register('upper', $normalizer);

        $this->assertSame($normalizer, $this->registry->resolve('upper'));
    }

    public function testResolveReturnsCallableKeyDirectly(): void
    {
        $result = $this->registry->resolve('strval');
        $this->assertSame('strval', $result);
    }

    public function testResolveThrowsForUnknownNonCallable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->registry->resolve('not_a_function_or_registered_key');
    }

    public function testRegisteredKeyTakesPrecedenceOverCallable(): void
    {
        $custom = fn($v) => 'custom:' . $v;
        $this->registry->register('strval', $custom);

        $resolved = $this->registry->resolve('strval');
        $this->assertSame($custom, $resolved);
        $this->assertSame('custom:hello', $resolved('hello'));
    }
}
