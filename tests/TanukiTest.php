<?php

namespace Tanuki\Tests;

use PHPUnit\Framework\TestCase;
use Tanuki\Tanuki;
use Tanuki\Validator;
use Tanuki\NormalizerRegistry;
use Tanuki\Factory\FieldFactory;
use Tanuki\Form;

class TanukiTest extends TestCase
{
    public function testDefaultConstruction(): void
    {
        $tanuki = new Tanuki();

        $this->assertInstanceOf(FieldFactory::class, $tanuki->fieldFactory);
        $this->assertInstanceOf(Validator::class, $tanuki->validator);
        $this->assertInstanceOf(NormalizerRegistry::class, $tanuki->normalizerRegistry);
    }

    public function testCustomOptionsInjection(): void
    {
        $validator = new Validator();
        $factory = new FieldFactory();
        $registry = new NormalizerRegistry();

        $tanuki = new Tanuki([
            'validator' => $validator,
            'fieldFactory' => $factory,
            'normalizerRegistry' => $registry,
        ]);

        $this->assertSame($validator, $tanuki->validator);
        $this->assertSame($factory, $tanuki->fieldFactory);
        $this->assertSame($registry, $tanuki->normalizerRegistry);
    }

    public function testCreateFormReturnsForm(): void
    {
        $tanuki = new Tanuki();
        $form = $tanuki->createForm([
            'schema' => [
                'name' => ['type' => 'value', 'validation' => ['required' => true]],
                'email' => ['type' => 'value', 'validation' => ['email' => true]],
            ],
        ]);

        $this->assertInstanceOf(Form::class, $form);
    }

    public function testCreateFormWithEmptySchema(): void
    {
        $tanuki = new Tanuki();
        $form = $tanuki->createForm(['schema' => []]);

        $this->assertInstanceOf(Form::class, $form);
    }
}
