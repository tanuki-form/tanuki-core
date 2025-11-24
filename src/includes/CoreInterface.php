<?php

namespace Tanuki;

interface CoreInterface {
  public function addFieldSchema();

  public function createForm(string $name, array $schema);
}
