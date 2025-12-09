<?php

namespace Tanuki\HelperMethod;

use Tanuki\FormResult;
use Tanuki\HelperMethodInterface;

class ResultViewer implements HelperMethodInterface {
  public function registerFor($helper): void {
    $helper->register("resultView", function(FormResult $formResult){
      include __DIR__ . "/views/results.php";
    });
  }
}
