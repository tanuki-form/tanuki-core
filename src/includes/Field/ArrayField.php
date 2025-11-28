<?php

namespace Tanuki\Field;

use Tanuki\NormalizerRegistry;

class ArrayField extends AbstractField {
  protected string $type = "array";

  public function normalize(mixed $value, NormalizerRegistry $registry): mixed {
    $callable = $registry->resolve($this->normalizerKey);
    return array_map($callable, $value);
  }
}
