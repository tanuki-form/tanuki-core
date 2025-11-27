<?php

namespace Tanuki\PostHandler;

use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;

class JsonLogHandler extends AbstractHandler {
  public array $config = [];

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    $dir = $this->config['directory'] ?? (rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '/') . '/logs');

    if(!file_exists($dir)){
      mkdir($dir, 0755);
    }

    if(!is_dir($dir)){
      return $this->failure("Path is not directory: {$dir}");
    }

    if(!is_writable($dir)){
      return $this->failure("Log file is not writable: {$dir}");
    }

    $path = "{$dir}/" . date("YmdHis") . "_" . bin2hex(random_bytes(4)) . ".json";
    $data = $form->getNormalizedData();
    file_put_contents($path, json_encode($data));

    return $this->success();
  }
}
