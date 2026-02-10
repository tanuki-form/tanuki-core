<?php

namespace Tanuki\Tests\Field;

use PHPUnit\Framework\TestCase;
use Tanuki\Field\StructField;
use Tanuki\Field\ValueField;
use Tanuki\NormalizerRegistry;

class StructFieldTest extends TestCase
{
    private NormalizerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new NormalizerRegistry();
        $this->registry->register('strval', 'strval');
    }

    public function testGetType(): void
    {
        $field = new StructField('address');
        $this->assertSame('struct', $field->getType());
    }

    public function testNormalizeReturnsNullForNonArray(): void
    {
        $field = new StructField('address');
        $this->assertNull($field->normalize('not-an-array', $this->registry));
        $this->assertNull($field->normalize(null, $this->registry));
        $this->assertNull($field->normalize(123, $this->registry));
    }

    public function testNormalizeCreatesStdClassFromArray(): void
    {
        $child1 = new ValueField('city');
        $child2 = new ValueField('zip');

        $field = new StructField('address');
        $field->fields = [$child1, $child2];

        $result = $field->normalize(
            ['city' => 'Tokyo', 'zip' => '100-0001'],
            $this->registry
        );

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertSame('Tokyo', $result->city);
        $this->assertSame('100-0001', $result->zip);
    }

    public function testNormalizeMissingNestedFieldReturnsNull(): void
    {
        $child = new ValueField('city');

        $field = new StructField('address');
        $field->fields = [$child];

        $result = $field->normalize([], $this->registry);

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertSame('', $result->city); // strval(null) = ''
    }
}
