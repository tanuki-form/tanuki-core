<?php

namespace Tanuki\Field;

use Tanuki\NormalizerRegistry;
use stdClass;

class StructField extends AbstractField {
  protected string $type = 'struct';
  public array $fields = [];

  public function normalize(mixed $value, NormalizerRegistry $registry): mixed {
    if (!is_array($value)) {
      // [TODO] エラー処理
    }

    $o = new stdClass();

    foreach ($this->fields as $field) {
      $fieldName = $field->name;
      $fieldValue = $value[$field->name] ?? null;
      $o->$fieldName = $field->normalize($fieldValue);
    }

    return $o;
  }
}
