<?php

namespace Tanuki;

interface HandlerInterface {
  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult;
}
