<?php

namespace Tanuki;

class FormResult {
  private function __construct(
    private bool $isSuccess,
    private array $preHandlerResults,
    private array $postHandlerResults,
    private array $validationErrors
  ) {}

  public static function fromContextAndForm(
    HandlerPipelineContext $preContext,
    HandlerPipelineContext $postContext,
    Form $form
  ): self {
    $isSuccess = !$preContext->hasError() && !$postContext->hasError() && !$form->hasValidationErrors();

    return new self(
      isSuccess: $isSuccess,
      preHandlerResults: $preContext->getResults(),
      postHandlerResults: $postContext->getResults(),
      validationErrors: $form->getValidationErrors()
    );
  }

  public function isSuccessful(): bool {
    return $this->isSuccess;
  }
  public function hasValidationErrors(): bool {
    return count($this->validationErrors) > 0;
  }

  public function getValidationErrors(): array {
    return $this->validationErrors;
  }

  /**
   * @return HandlerResult[]
   */
  public function getPreHandlerResults(): array {
    return $this->preHandlerResults;
  }

  /**
   * @return HandlerResult[]
   */
  public function getPostHandlerResults(): array {
    return $this->postHandlerResults;
  }
}
