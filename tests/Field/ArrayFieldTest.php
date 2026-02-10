<?php

namespace Tanuki\Tests\Field;

use PHPUnit\Framework\TestCase;
use Tanuki\Field\ArrayField;
use Tanuki\NormalizerRegistry;

class ArrayFieldTest extends TestCase
{
    public function testGetType(): void
    {
        $field = new ArrayField('tags');
        $this->assertSame('array', $field->getType());
    }

    public function testNormalizeAppliesCallableToEachElement(): void
    {
        $registry = new NormalizerRegistry();
        $registry->register('strval', 'strval');

        $field = new ArrayField('ids');
        $result = $field->normalize([1, 2, 3], $registry);

        $this->assertSame(['1', '2', '3'], $result);
    }

    public function testNormalizeWithCustomCallable(): void
    {
        $registry = new NormalizerRegistry();
        $registry->register('strval', fn($v) => trim((string)$v));

        $field = new ArrayField('items');
        $result = $field->normalize([' a ', ' b '], $registry);

        $this->assertSame(['a', 'b'], $result);
    }

    public function testNormalizeReturnsEmptyArrayForNonArray(): void
    {
        $registry = new NormalizerRegistry();

        $field = new ArrayField('tags');
        $this->assertSame([], $field->normalize('not-an-array', $registry));
        $this->assertSame([], $field->normalize(null, $registry));
        $this->assertSame([], $field->normalize(123, $registry));
    }

    public function testNormalizeEmptyArray(): void
    {
        $registry = new NormalizerRegistry();
        $registry->register('strval', 'strval');

        $field = new ArrayField('tags');
        $this->assertSame([], $field->normalize([], $registry));
    }
}
