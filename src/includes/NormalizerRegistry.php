<?php

namespace Tanuki;

class NormalizerRegistry {
  public array $normalizers = [];

  public function register(string $key, callable $normalizer): void {
    $this->normalizers[$key] = $normalizer;
  }

  public function resolve(string $key) {
    if(in_array($key, $this->normalizers)) {
      return $this->normalizers[$key];
    }

    if(is_callable($key)) {
      return $key;
    }

    throw new \InvalidArgumentException(
      sprintf('Normalizer "%s" not found in registry or as a callable function.', $key)
    );
  }
}