<?php

namespace Tanuki\PreHandler;

use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;
use Tanuki\Helper;

class TurnstileHandler extends AbstractHandler {
  public array $config = [];

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    if($context->hasError()) return $this->skipped();

    $formData = $form->getRawData();
    $token = $formData["cf-turnstile-response"] ?? "";

    if(!$token){
      return $this->failure("token-missing");
    }

    $payload = [
      "response" => $token,
      "secret" => $this->config["secretKey"] ?? "",
      "remoteip" => $_SERVER["REMOTE_ADDR"] ?? null,
    ];

    $ch = curl_init("https://challenges.cloudflare.com/turnstile/v0/siteverify");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);

    $result = json_decode($response, true);

    if(!($result["success"] ?? false)){
      return $this->failure("verification-failed");
    }

    return $this->success();
  }

  public function registerHelper(Helper $helper): void {
    $handler = $this;

    $helper->register("getTurnstileSiteKey", function()use($handler) {
      return $this->config["siteKey"];
    });
  }
}
