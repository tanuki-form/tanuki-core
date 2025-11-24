<?php

namespace Tanuki;

use Tanuki\Factory\FieldFactory;
use Tanuki\Field\AbstractField;

class FormSchema {
  /** @var AbstractField[] */
  public array $fields;

  public function __construct() {
  }

  public function addField(AbstractField $field){
    $this->fields[] = $field;
  }

  public static function fromArray(array $schema, FieldFactory $fieldFactory): self {
    $formSchema = new self;

    foreach($schema as $_field){
      $formSchema->addField($fieldFactory->create($_field));
    }

    return $formSchema;
  }
}
