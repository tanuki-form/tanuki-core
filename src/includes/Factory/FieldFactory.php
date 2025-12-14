<?php

namespace Tanuki\Factory;

use Tanuki\Field\FieldInterface;
use Tanuki\Field\ValueField;
use Tanuki\Field\ArrayField;
use Tanuki\Field\FileField;
use Tanuki\Field\StructField;

class FieldFactory
{
  /** @var array<string, string> */
  private array $typeMap = [];

  public function __construct() {
    $this->registerField("value", ValueField::class);
    $this->registerField("array", ArrayField::class);
    $this->registerField("file", FileField::class);
    $this->registerField("struct", StructField::class);
  }

  public function registerField(string $type, string $className): void{
    if (!is_a($className, FieldInterface::class, true)) {
      throw new \InvalidArgumentException("Class {$className} must implement FieldInterface.");
    }
    $this->typeMap[$type] = $className;
  }

  public function create(array $fieldData): FieldInterface {
    $type = $fieldData["type"] ?? "value";

    if (!isset($this->typeMap[$type])) {
      throw new \InvalidArgumentException("Unsupported field type: {$type}. Is the field registered?");
    }

    $className = $this->typeMap[$type];
    $field = new $className($fieldData["name"]);

    foreach($fieldData["validation"] ?? [] as $name => $args){
      $field->addValidation($name, $args);
    }

    return $field;
  }
}
