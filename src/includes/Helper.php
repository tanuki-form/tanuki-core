<?php

namespace Tanuki;

class Helper {
  private array $helpers = [];

  public function register($name, $helper){
    $this->helpers[$name] = $helper;
  }

  public function __call(string $name, array $arguments): mixed{
    if(isset($this->helpers[$name])){
      return ($this->helpers[$name])(...$arguments);
    }

    throw new \BadMethodCallException("Helper '{$name}' not registered.");
  }
}