<?php

namespace Tanuki;

class AbstractHandler implements HandlerInterface {
  public function __construct(
    protected array $config = [],
    protected string $action = "handle"
  ) {}

  protected function success(array $data=[]): HandlerResult {
    return HandlerResult::success(static::class, $data);
  }

  protected function failure(string $message="", array $data=[]): HandlerResult {
    return HandlerResult::failure(static::class, $message, $data);
  }

  protected function skipped(): HandlerResult {
    return HandlerResult::skipped(static::class);
  }

  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    return $this->success();
  }

  final public function __handle(Form $form, HandlerPipelineContext $ctx): void {
    $method = $this->action;

    if (method_exists($this, $method)) {
      $this->$method($form, $ctx);
      return;
    }

    throw new \RuntimeException(sprintf(
      "Method [%s] が %s に存在しません。",
      $method,
      get_class($this)
    ));
  }

  public function registerHelper(Helper $helper): void {}
}
