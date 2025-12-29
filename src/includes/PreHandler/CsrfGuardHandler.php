<?php

namespace Tanuki\PreHandler;

use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\Helper;

class CsrfGuardHandler extends AbstractHandler {
  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    if($context->hasError()) return $this->skipped();
    if(session_status() === PHP_SESSION_NONE) session_start();

    $formData = $form->getRawData();
    $key = $this->config["token-session-key"] ?? "csrf-token";

    if(empty($formData["csrf-token"])){
      return $this->failure("no-token");
    }

    if($formData["csrf-token"] !== ($_SESSION[$key] ?? "")){
      return $this->failure("invalid-token");
    }

    return $this->success();
  }

  public function registerHelper(Helper $helper): void {
    $helper->register("getCsrfToken", function() {
      if(session_status() === PHP_SESSION_NONE) session_start();

      $key = $this->config["token-session-key"] ?? "csrf-token";

      if(empty($_SESSION[$key])){
        $_SESSION[$key] = bin2hex(random_bytes(32));
      }

      return $_SESSION[$key];
    });
  }
}
