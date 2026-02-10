<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\FormSchema;
use Tanuki\Factory\FieldFactory;
use Tanuki\Field\ValueField;

class FormSchemaTest extends TestCase
{
    public function testFromArrayCreatesFields(): void
    {
        $schema = FormSchema::fromArray([
            'name' => ['type' => 'value'],
            'email' => ['type' => 'value'],
        ], new FieldFactory());

        $this->assertCount(2, $schema->fields);
        $this->assertSame('name', $schema->fields[0]->name);
        $this->assertSame('email', $schema->fields[1]->name);
    }

    public function testFromArraySetsNameFromKey(): void
    {
        $schema = FormSchema::fromArray([
            'myField' => ['type' => 'value'],
        ], new FieldFactory());

        $this->assertSame('myField', $schema->fields[0]->name);
    }

    public function testFromArrayUsesExplicitName(): void
    {
        $schema = FormSchema::fromArray([
            'key' => ['type' => 'value', 'name' => 'explicit_name'],
        ], new FieldFactory());

        $this->assertSame('explicit_name', $schema->fields[0]->name);
    }

    public function testFromArrayWithValidation(): void
    {
        $schema = FormSchema::fromArray([
            'email' => [
                'type' => 'value',
                'validation' => ['required' => true, 'email' => true],
            ],
        ], new FieldFactory());

        $this->assertSame(['required' => true, 'email' => true], $schema->fields[0]->validators);
    }

    public function testAddField(): void
    {
        $schema = FormSchema::fromArray([], new FieldFactory());
        $schema->addField(new ValueField('extra'));

        $this->assertCount(1, $schema->fields);
        $this->assertSame('extra', $schema->fields[0]->name);
    }
}
