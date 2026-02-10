<?php

namespace Tanuki\Tests\Field;

use PHPUnit\Framework\TestCase;
use Tanuki\Field\FileField;
use Tanuki\NormalizerRegistry;

class FileFieldTest extends TestCase
{
    public function testGetType(): void
    {
        $field = new FileField('attachment');
        $this->assertSame('file', $field->getType());
    }

    public function testNormalizeReturnsValueAsIs(): void
    {
        $registry = new NormalizerRegistry();
        $field = new FileField('attachment');

        $fileData = ['name' => 'test.pdf', 'size' => 1024];
        $this->assertSame($fileData, $field->normalize($fileData, $registry));
    }
}
