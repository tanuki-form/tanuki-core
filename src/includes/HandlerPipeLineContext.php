<?php

namespace Tanuki;

class HandlerPipelineContext {
  // @var HandlerResult[] //
  private array $results = [];
  private bool $hasError = false;
  private SharedData $sharedData;

  public function __construct($sharedData = new SharedData()) {
    $this->sharedData = $sharedData;
  }

  public function addResult(HandlerResult $result): void {
    $this->results[] = $result;

    if ($result->isFailure()) {
      $this->hasError = true;
    }
  }

  public function hasError(): bool {
    return $this->hasError;
  }

  public function getResults(): array {
    return $this->results;
  }

  public function get(string $key) {
    return $this->sharedData->get($key) ?? null;
  }

  public function set(string $key, $value): void {
    $this->sharedData->set($key, $value);
  }
}
