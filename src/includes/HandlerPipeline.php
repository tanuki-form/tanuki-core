<?php

namespace Tanuki;

class HandlerPipeline {
  /** @var HandlerInterface[] */
  private array $handlers = [];

  public function __construct(
    private string $type
  ) {}

  public function addHandler(HandlerInterface $handler): void {
    $this->handlers[] = $handler;
  }

  public function execute(Form $form, HandlerPipelineContext $context): void {
    foreach($this->handlers as $handler) {
      $context->addResult($handler->handle($form, $context));
    }
  }
}
