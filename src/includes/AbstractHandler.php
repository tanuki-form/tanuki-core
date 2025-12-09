<?php

namespace Tanuki;

class AbstractHandler implements HandlerInterface {
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

  public function registerHelper(Helper $helper): void {}
}
