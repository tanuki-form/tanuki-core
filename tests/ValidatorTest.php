<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\Validator;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    // --- required ---

    public function testRequiredPassesWithValue(): void
    {
        $this->assertTrue($this->validator->validate('required', 'hello', []));
    }

    public function testRequiredFailsWithEmptyString(): void
    {
        $this->assertFalse($this->validator->validate('required', '', []));
    }

    public function testRequiredFailsWithWhitespace(): void
    {
        $this->assertFalse($this->validator->validate('required', '   ', []));
    }

    public function testRequiredPassesWithNonEmptyArray(): void
    {
        $this->assertTrue($this->validator->validate('required', ['a'], []));
    }

    public function testRequiredFailsWithEmptyArray(): void
    {
        $this->assertFalse($this->validator->validate('required', [], []));
    }

    // --- email ---

    public function testEmailPassesWithValidEmail(): void
    {
        $this->assertTrue($this->validator->validate('email', 'test@example.com', []));
    }

    public function testEmailFailsWithInvalidEmail(): void
    {
        $this->assertFalse($this->validator->validate('email', 'not-an-email', []));
    }

    public function testEmailFailsWithArray(): void
    {
        $this->assertFalse($this->validator->validate('email', ['test@example.com'], []));
    }

    // --- minLength ---

    public function testMinLengthPasses(): void
    {
        $this->assertTrue($this->validator->validate('minLength', 'abc', [], 3));
    }

    public function testMinLengthFailsWhenTooShort(): void
    {
        $this->assertFalse($this->validator->validate('minLength', 'ab', [], 3));
    }

    public function testMinLengthExactBoundary(): void
    {
        $this->assertTrue($this->validator->validate('minLength', 'ab', [], 2));
    }

    public function testMinLengthFailsWithArray(): void
    {
        $this->assertFalse($this->validator->validate('minLength', ['a', 'b'], [], 1));
    }

    // --- maxLength ---

    public function testMaxLengthPasses(): void
    {
        $this->assertTrue($this->validator->validate('maxLength', 'ab', [], 3));
    }

    public function testMaxLengthFailsWhenTooLong(): void
    {
        $this->assertFalse($this->validator->validate('maxLength', 'abcd', [], 3));
    }

    public function testMaxLengthExactBoundary(): void
    {
        $this->assertTrue($this->validator->validate('maxLength', 'abc', [], 3));
    }

    // --- matchField ---

    public function testMatchFieldPasses(): void
    {
        $postData = ['password' => 'secret', 'confirm' => 'secret'];
        $this->assertTrue($this->validator->validate('matchField', 'secret', $postData, 'password'));
    }

    public function testMatchFieldFailsWhenDifferent(): void
    {
        $postData = ['password' => 'secret', 'confirm' => 'wrong'];
        $this->assertFalse($this->validator->validate('matchField', 'wrong', $postData, 'password'));
    }

    public function testMatchFieldFailsWhenReferenceFieldMissing(): void
    {
        $this->assertFalse($this->validator->validate('matchField', 'value', [], 'nonexistent'));
    }

    // --- numeric ---

    public function testNumericPassesWithNumericString(): void
    {
        $this->assertTrue($this->validator->validate('numeric', '123', []));
    }

    public function testNumericPassesWithFloat(): void
    {
        $this->assertTrue($this->validator->validate('numeric', '12.5', []));
    }

    public function testNumericFailsWithNonNumeric(): void
    {
        $this->assertFalse($this->validator->validate('numeric', 'abc', []));
    }

    public function testNumericFailsWithArray(): void
    {
        $this->assertFalse($this->validator->validate('numeric', ['123'], []));
    }

    // --- inArray ---

    public function testInArrayPassesWhenValueInOptions(): void
    {
        $this->assertTrue($this->validator->validate('inArray', 'a', [], ['a', 'b', 'c']));
    }

    public function testInArrayFailsWhenValueNotInOptions(): void
    {
        $this->assertFalse($this->validator->validate('inArray', 'd', [], ['a', 'b', 'c']));
    }

    public function testInArrayPassesWithArrayValueAllInOptions(): void
    {
        $this->assertTrue($this->validator->validate('inArray', ['a', 'b'], [], ['a', 'b', 'c']));
    }

    public function testInArrayFailsWithArrayValueNotAllInOptions(): void
    {
        $this->assertFalse($this->validator->validate('inArray', ['a', 'd'], [], ['a', 'b', 'c']));
    }

    // --- pattern ---

    public function testPatternPasses(): void
    {
        $this->assertTrue($this->validator->validate('pattern', '123-4567', [], '^\d{3}-\d{4}$'));
    }

    public function testPatternFails(): void
    {
        $this->assertFalse($this->validator->validate('pattern', 'abc', [], '^\d+$'));
    }

    public function testPatternFailsWithArray(): void
    {
        $this->assertFalse($this->validator->validate('pattern', ['123'], [], '^\d+$'));
    }

    // --- custom validator ---

    public function testAddCustomValidator(): void
    {
        $this->validator->addValidator('even', function (string|array $value, array $postData, mixed $arg): bool {
            return is_numeric($value) && (int)$value % 2 === 0;
        });

        $this->assertTrue($this->validator->validate('even', '4', []));
        $this->assertFalse($this->validator->validate('even', '3', []));
    }

    // --- unknown validator ---

    public function testValidateThrowsForUnknownValidator(): void
    {
        $this->expectException(\Exception::class);
        $this->validator->validate('nonexistent', 'value', []);
    }
}
