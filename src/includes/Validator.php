<?php

namespace Tanuki;

class Validator {
  public array $validators = [];

  public function __construct() {
    $this->addValidator('required', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value)) {
        return !empty($value);
      }
      return !is_null($value) && trim($value) !== '';
    });

    $this->addValidator('email', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value)) {
        return false;
      }
      return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    });

    $this->addValidator('minLength', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value) || !is_numeric($arg)) {
        return false;
      }
      return strlen($value) >= $arg;
    });

    $this->addValidator('maxLength', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value) || !is_numeric($arg)) {
        return false;
      }
      return strlen($value) <= $arg;
    });

    $this->addValidator('matchField', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value) || !isset($postData[$arg])) {
        return false;
      }
      $otherValue = $postData[$arg] ?? null;
      return $value === $otherValue;
    });

    $this->addValidator('numeric', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value)) {
        return false;
      }
      return is_numeric($value);
    });

    $this->addValidator('inArray', function(string|array $value, array $postData, mixed $arg = true): bool {
      if ($arg[0] === true) {
        return false;
      }
      $options = $arg;
      if (is_array($value)) {
        foreach ($value as $v) {
          if (!in_array($v, $options)) {
            return false;
          }
        }
        return true;
      } else {
        return in_array($value, $options);
      }
    });

    $this->addValidator('pattern', function(string|array $value, array $postData, mixed $arg = true): bool {
      if (is_array($value) || !is_string($arg)) {
        return false;
      }
      $pattern = $arg;
      return preg_match("/" . str_replace("/", "\\/", $pattern) . "/", $value) === 1;
    });
  }

  public function validate(string $validatorName, string|array $value, array $postData, mixed $arg = []): bool {
    if (!isset($this->validators[$validatorName])) {
      throw new \Exception("Validator {$validatorName} not found");
    }

    $function = $this->validators[$validatorName];
    return $function($value, $postData, $arg);
  }

  public function addValidator(string $name, callable $function): void {
    $this->validators[$name] = $function;
  }
}
