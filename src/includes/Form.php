<?php

namespace Tanuki;

class Form {
  public string $name;
  private FormSchema $schema;
  private Validator $validator;
  private HandlerPipeline $preHandlers;
  private HandlerPipeline $postHandlers;
  private NormalizerRegistry $normalizerRegistry;

  private array $postData;
  private array $validationErrors = [];

  public function __construct(string $name, FormSchema $schema, Validator $validator, NormalizerRegistry $normalizerRegistry) {
    $this->name = $name;
    $this->schema = $schema;
    $this->validator = $validator;
    $this->normalizerRegistry = $normalizerRegistry;
    $this->preHandlers = new HandlerPipeline("pre");
    $this->postHandlers = new HandlerPipeline("post");
  }

  public function addPreHandler(HandlerInterface $preHandler){
    $this->preHandlers->addHandler($preHandler);
  }

  public function addPostHandler(HandlerInterface $postHandler){
    $this->postHandlers->addHandler($postHandler);
  }

  public function bind(array $data){
    $this->postData = [];

    foreach($this->schema->fields as $field){
      $this->postData[$field->name] = $data[$field->name] ?? null;
    }
  }

  public function getRawData(): array {
    return $this->postData;
  }

  public function getNormalizedData(): array {
    $postData = $this->postData;
    $data = [];

    foreach($this->schema->fields as $field){
      $fieldName = $field->name;
      $fieldValue = $postData[$fieldName] ?? null;
      $data[$fieldName] = $field->normalize($fieldValue, $this->normalizerRegistry);
    }

    return $data;
  }
  public function hasValidationErrors(): bool {
    return !empty($this->validationErrors);
  }

  public function getValidationErrors(): array {
    return $this->validationErrors;
  }

  public function validate(){
    $success = true;

    foreach($this->schema->fields as $field){
      $value = $this->postData[$field->name] ?? '';

      foreach($field->validators as $validatorName => $args){
        $isValid = $this->validator->validate($validatorName, $value, $this->postData, $args);
        if(!$isValid){
          $success = false;
          $this->addValidationError($field->name, $validatorName);
        }
      }
    }

    return $success;
  }

  public function send(): FormResult {
    $sharedData = new SharedData();
    $preContext = new HandlerPipelineContext($sharedData);
    $postContext = new HandlerPipelineContext($sharedData);

    $this->preHandlers->execute($this, $preContext);

    if(!$preContext->hasError()){
      if($this->validate()){
        $this->postHandlers->execute($this, $postContext);
      }
    }

    return FormResult::fromContextAndForm($preContext, $postContext, $this);
  }

  private function addValidationError(string $field, string $vname){
    if(isset($this->errors[$field])){
      $this->validationErrors[$field] = [$vname];
    }else{
      $this->validationErrors[$field][] = $vname;
    }
  }
}
