<?php

namespace Tanuki;

class Tanuki {
  public Factory\FieldFactory $fieldFactory;
  public Validator $validator;
  public NormalizerRegistry $normalizerRegistry;

  public function __construct(array $options=[]) {
    $this->fieldFactory = $options["fieldFactory"] ?? new Factory\FieldFactory();
    $this->validator = $options["validator"] ?? new Validator();
    $this->normalizerRegistry = $options["normalizerRegistry"] ?? new NormalizerRegistry();
  }

  public function createForm(array $config=[]): Form {
    $formSchema = FormSchema::fromArray($config["schema"], $this->fieldFactory);
    $form = new Form($formSchema, $this->validator, $this->normalizerRegistry);

    foreach($config["preHandlers"] ?? [] as $o){
      $form->addPreHandler(new $o["handler"]($o["config"]));
    }

    foreach($config["postHandlers"] ?? [] as $o){
      $form->addPostHandler(new $o["handler"]($o["config"]));
    }

    foreach($config["helpers"] ?? [] as $helperMethod){
      /**
       * @var HelperMethodInterface $helperMethod
       */
      $helperMethod = new $helperMethod;
      $helperMethod->registerFor($form->helper);
    }

    return $form;
  }
}
