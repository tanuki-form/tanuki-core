<?php

namespace Tanuki\PostHandler;

use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;

class RecaptchaHandler extends AbstractHandler {
  public array $config = [];

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    if($context->hasError()) return $this->skipped();

    $formData = $form->getRawData();
    $token = $formData['recaptchaToken'] ?? '';

    if(empty($token)){
      $this->failure('token-missing');
    }

    $projectId = $this->config['projectId'] ?? '';
    $apiKey = $this->config['apiKey'] ?? '';
    $siteKey = $this->config['siteKey'] ?? '';
    $action = $this->config['action'] ?? '';

    $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$projectId}/assessments?key={$apiKey}";

    $payload = [
      'event' => [
        'token'          => $token,
        'siteKey'        => $siteKey,
        'expectedAction' => $action
      ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);

    if($error){
      return $this->failure('curl-error', ['error' => $error]);
    }

    $result = json_decode($response, true);
    $score   = $result['riskAnalysis']['score'] ?? null;
    $reasons = $result['riskAnalysis']['reasons'] ?? [];

    if ($score === null) {
      return $this->failure('invalid-assessment');
    }

    if($score < 0.5){
      return $this->failure('rejected');
    }

    return $this->success();
  }
}
