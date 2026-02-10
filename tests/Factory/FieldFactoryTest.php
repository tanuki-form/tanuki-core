<?php

namespace Tanuki\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Tanuki\Factory\FieldFactory;
use Tanuki\Field\ValueField;
use Tanuki\Field\ArrayField;
use Tanuki\Field\FileField;
use Tanuki\Field\StructField;
use Tanuki\Field\FieldInterface;

class FieldFactoryTest extends TestCase
{
    private FieldFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FieldFactory();
    }

    public function testCreateValueField(): void
    {
        $field = $this->factory->create(['name' => 'email', 'type' => 'value']);
        $this->assertInstanceOf(ValueField::class, $field);
        $this->assertSame('email', $field->name);
    }

    public function testCreateDefaultsToValueType(): void
    {
        $field = $this->factory->create(['name' => 'name']);
        $this->assertInstanceOf(ValueField::class, $field);
    }

    public function testCreateArrayField(): void
    {
        $field = $this->factory->create(['name' => 'tags', 'type' => 'array']);
        $this->assertInstanceOf(ArrayField::class, $field);
    }

    public function testCreateFileField(): void
    {
        $field = $this->factory->create(['name' => 'file', 'type' => 'file']);
        $this->assertInstanceOf(FileField::class, $field);
    }

    public function testCreateStructField(): void
    {
        $field = $this->factory->create(['name' => 'address', 'type' => 'struct']);
        $this->assertInstanceOf(StructField::class, $field);
    }

    public function testCreateWithValidation(): void
    {
        $field = $this->factory->create([
            'name' => 'email',
            'type' => 'value',
            'validation' => [
                'required' => true,
                'email' => true,
            ],
        ]);

        $this->assertSame(['required' => true, 'email' => true], $field->validators);
    }

    public function testCreateThrowsForUnsupportedType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create(['name' => 'x', 'type' => 'unknown']);
    }

    public function testRegisterCustomFieldType(): void
    {
        $this->factory->registerField('custom', ValueField::class);
        $field = $this->factory->create(['name' => 'x', 'type' => 'custom']);
        $this->assertInstanceOf(ValueField::class, $field);
    }

    public function testRegisterFieldThrowsForNonFieldInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->registerField('bad', \stdClass::class);
    }
}
