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

  public function createForm(string $name, array $options = []): Form {
    $formSchema = FormSchema::fromArray($options["schema"], $this->fieldFactory);
    $form = new Form($name, $formSchema, $this->validator, $this->normalizerRegistry);

    foreach($options["preHandlers"] ?? [] as $o){
      $form->addPreHandler(new $o["handler"]($o["config"]));
    }

    foreach($options["postHandlers"] ?? [] as $o){
      $form->addPostHandler(new $o["handler"]($o["config"]));
    }

    return $form;
  }
}
