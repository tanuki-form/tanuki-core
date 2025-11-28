<?php

namespace Tanuki\Field;

use Tanuki\NormalizerRegistry;

abstract class AbstractField implements FieldInterface {
  protected string $type;
  public string $name;
  public string $label;
  public array $validators = [];
  protected string $normalizerKey = "strval";

  public function __construct(string $name, string $label) {
    $this->name = $name;
    $this->label = $label;
  }

  public function getType(): string {
    return $this->type;
  }

  public function addValidation(string $name, mixed $args = null): self {
    $this->validators[$name] = $args;
    return $this;
  }

  public function addValidations(array $validations): self {
    foreach($validations as $name => $args){
      $this->addValidation($name, $args);
    }
    return $this;
  }

  public function normalize(mixed $value, NormalizerRegistry $registry): mixed {
    return $value;
  }
}
