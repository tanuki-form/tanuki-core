<?php

namespace Tanuki\Tests\Field;

use PHPUnit\Framework\TestCase;
use Tanuki\Field\ValueField;
use Tanuki\NormalizerRegistry;

class ValueFieldTest extends TestCase
{
    public function testGetType(): void
    {
        $field = new ValueField('name');
        $this->assertSame('value', $field->getType());
    }

    public function testName(): void
    {
        $field = new ValueField('email');
        $this->assertSame('email', $field->name);
    }

    public function testAddValidation(): void
    {
        $field = new ValueField('name');
        $result = $field->addValidation('required', true);

        $this->assertSame($field, $result);
        $this->assertSame(['required' => true], $field->validators);
    }

    public function testAddValidations(): void
    {
        $field = new ValueField('name');
        $field->addValidations([
            'required' => true,
            'minLength' => 2,
        ]);

        $this->assertSame(['required' => true, 'minLength' => 2], $field->validators);
    }

    public function testNormalizeWithRegisteredNormalizer(): void
    {
        $registry = new NormalizerRegistry();
        $registry->register('strval', 'strval');

        $field = new ValueField('age');
        $result = $field->normalize(123, $registry);

        $this->assertSame('123', $result);
    }

    public function testNormalizeWithCustomNormalizer(): void
    {
        $registry = new NormalizerRegistry();
        $registry->register('strval', fn($v) => strtoupper((string)$v));

        $field = new ValueField('name');
        $result = $field->normalize('hello', $registry);

        $this->assertSame('HELLO', $result);
    }
}
