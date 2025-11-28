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
    $path = $this->config["path"] ?? (rtrim(dirname($_SERVER["SCRIPT_FILENAME"]), "/") . "/log.tsv");

    if(file_exists($path)){
      if(is_dir($path) || !is_writable($path)){
        return $this->failure("Log file is not writable: {$path}");
      }
    }else{
      if(!touch($path)){
        return $this->failure("Log file could not be created: {$path}");
      }
    }

    $output = fopen($path, "a");
    $data = $form->getNormalizedData();
    $data = array_map(function($e){return is_array($e) ? implode(",", $e) : $e;}, $data);
    fputcsv($output, $data, "\t");
    fclose($output);

    return $this->success();
  }
}
