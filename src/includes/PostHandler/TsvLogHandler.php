<?php

namespace Tanuki\PostHandler;

use Tanuki\AbstractHandler;
use Tanuki\Form;
use Tanuki\HandlerPipelineContext;
use Tanuki\HandlerResult;

class TsvLogHandler extends AbstractHandler {
  public array $config = [];

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function handle(Form $form, HandlerPipelineContext $context): HandlerResult {
    $path = rtrim($this->config['path'] ?? dirname($_SERVER['SCRIPT_FILENAME']), '/') . '/log.tsv';

    if(file_exists($path) && (is_dir($path) || !is_writable($path))) {
      return $this->failure("Log file is not writable: " . $path);
    }

    if(!file_exists($path) && !touch($path)) {
      return $this->failure("Log file could not be created: " . $path);
    }

    $output = fopen($path, 'a');
    fputcsv($output, $form->getNormalizedData(), "\t");
    fclose($output);

    return $this->success();
  }
}
