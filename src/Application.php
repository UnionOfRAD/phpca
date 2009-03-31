<?php 

/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 * 
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software 
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPca
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 * @license    BSD License
 */

namespace spriebsch\PHPca;

class Application
{
  protected $version = '0.1.0';

  protected $path;
  protected $phpExecutable;

  protected $positionCount = 0;


  protected function createFileList($path)
  {
    if (is_file($path)) {
      return array($path);
    }

    $list = array();

    $it = new \DirectoryIterator($path);

    foreach ($it as $file) {
      if (!$file->isFile()) {
        continue;
      }

      if (substr($file->getPathname(), -4) != '.php') {
        continue;
      }

      $list[] = $file->getPathname();
    }

    return $list;
  }


  protected function printUsage()
  {
    echo file_get_contents(__DIR__ . '/usage.txt');
  }


  protected function printLetter($letter = '.')
  {
    if ($this->positionCount > 58) {
      echo PHP_EOL;
      $this->positionCount = 0;
    }

    echo $letter;
    $this->positionCount++;
  }


  protected function parseCommandLine($arguments)
  {
    $arguments = array_slice($arguments, 1);

    for ($i = 0; $i < count($arguments); $i++) {
    
      switch ($arguments[$i]) {
        case '-h':
          $this->printUsage();
          exit();
        break;

        case '-p':
          $i++;
          $this->phpExecutable = $arguments[$i];
        break;
      }

      $last_argument = array_slice($arguments, -1);
      $this->path = $last_argument[0];
    }

    if ($this->path == '') {
      throw new \RuntimeException('Missing argument: no file or directory to analyze');
    }

    if ($this->phpExecutable == '') {
      throw new \RuntimeException('Missing argument: path to PHP executable (-p) must be specified');
    }
  }


  protected function runLintCheck($file)
  {
    $output = trim(shell_exec($this->phpExecutable . ' -l ' . escapeshellarg($file) . ' 2>/dev/null'));

    $cmp = 'No syntax errors';
    if (substr($output, 0, strlen($cmp)) == $cmp) {
      return '';
    }

    return $output;
  }


  protected function doRun()
  {
    if (!file_exists($this->phpExecutable)) {
      throw new \RuntimeException('PHP executable ' . $this->phpExecutable . ' not found');
    }

    if (!is_executable($this->phpExecutable)) {
      throw new \RuntimeException('PHP executable ' . $this->phpExecutable . ' not executable');
    }


    Constants::init();

    $tokenizer = new Tokenizer();
    $result = new Result();

    $files = $this->createFileList($this->path);

    foreach ($files as $file) {

      $lintResult = $this->runLintCheck($file);

      if ($lintResult != '') {
        $this->printLetter('E');
        $result->addError($file, $lintResult);
        continue;
      }

      $f = $tokenizer->tokenize($file, file_get_contents($file));
//      var_dump($f->getTokenSequence());

// apply rules on the token sequence.
// store errors

// on error, output F letter. (W letter for warning)
// (when warning level)

// if no errors:
      $this->printLetter();
      $result->addFile($file);
    }

    echo PHP_EOL;
  var_dump($result);

    if ($result->hasErrors()) {
      echo ': ' . PHP_EOL . PHP_EOL;
      return;
    }

    if ($result->hasWarnings()) {
      echo ': ' . PHP_EOL . PHP_EOL;
      return;
    }

    echo 'OK' . PHP_EOL . PHP_EOL;
  }


  public function run($arguments)
  {
    echo 'PHP Code Analyzer ' . $this->version . ' by Stefan Priebsch' . PHP_EOL . PHP_EOL;

    try {
      $this->parseCommandLine($arguments);
      $this->doRun();
    }

    catch (\RuntimeException $e) {
      echo $e->getMessage() . PHP_EOL . PHP_EOL;
      $this->printUsage();
      exit();
    }
  }
}
  
?>
