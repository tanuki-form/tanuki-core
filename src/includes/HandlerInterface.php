<?php

namespace Tanuki;

interface HandlerInterface {
  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult;
  public function __handle(Form $form, HandlerPipelineContext $context): HandlerResult;
  public function registerHelper(Helper $helper): void;
}
