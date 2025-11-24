<?php

namespace Tanuki;

class Tanuki {
  public Factory\FieldFactory $fieldFactory;
  public Validator $validator;
  public NormalizerRegistry $normalizerRegistry;

  public function __construct(array $config=[]) {
    $this->fieldFactory = $config["fieldFactory"] ?? new Factory\FieldFactory();
    $this->validator = $config["validator"] ?? new Validator();
    $this->normalizerRegistry = $config["normalizerRegistry"] ?? new NormalizerRegistry();
  }

  public function createForm(string $name, array $schema): Form {
    $formSchema = FormSchema::fromArray($schema, $this->fieldFactory);
    $form = new Form($name, $formSchema, $this->validator, $this->normalizerRegistry);

    return $form;
  }
}
