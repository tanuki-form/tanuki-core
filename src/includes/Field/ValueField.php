<?php

namespace Tanuki\Field;

use Tanuki\NormalizerRegistry;

class ValueField extends AbstractField {
  protected string $type = 'value';
  protected string $normalizerKey = 'strval';

  public function normalize(mixed $value, NormalizerRegistry $registry): mixed {
    $callable = $registry->resolve($this->normalizerKey);
    return call_user_func($callable, $value);
  }
}
