<?php

namespace Tanuki;

class HandlerResult {
  private function __construct(
    private string  $identifier,
    private bool $isSuccessful,
    private bool $wasSkipped,
    private ?string $errorMessage = null,
    private array $data = []
  ) {}

  public static function success(string $identifier, array $data = []): self {
    return new self(identifier: $identifier, isSuccessful: true, wasSkipped: false, data: $data);
  }

  public static function failure(string $identifier, string $message, array $data = []): self {
    return new self(identifier: $identifier, isSuccessful: false, wasSkipped: false, errorMessage: $message, data: $data);
  }

  public static function skipped(string $identifier): self {
    return new self(identifier: $identifier, isSuccessful: true, wasSkipped: true);
  }

  // getter
  public function isSuccessful(): bool { return $this->isSuccessful; }
  public function wasSkipped(): bool { return $this->wasSkipped; }
  public function isFailure(): bool { return !$this->isSuccessful; }
}
