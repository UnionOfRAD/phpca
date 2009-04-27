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
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
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

class Command
{
  protected $version = '0.2.0';

  protected $path;

  /**
   * Path to a directory containing the rules.
   */
  protected $rulePath = 'PHPCA/Rules';

  protected $phpExecutable;

  protected $positionCount = 0;

  protected $files;

  protected $result;


  protected function loadRules()
  {
    $list = array();

    $it = new \DirectoryIterator($this->rulePath);

    foreach ($it as $file) {
      if (!$file->isFile()) {
        continue;
      }

      if (substr($file->getPathname(), -4) != '.php') {
        continue;
      }

      $classname = __NAMESPACE__ . '\\' . substr($file->getFilename(), 0, -4);

      require_once $this->rulePath . DIRECTORY_SEPARATOR . $file->getFilename();

      if (!class_exists($classname)) {
        throw new Exception('File ' . $file->getFilename() . ' does not contain rule class ' . $classname);
      }

      $list[] = new $classname;
    }

    return $list;
  }


  protected function printUsage()
  {
    print 'Usage: phpca -p path_to_php <file to analyze>' . PHP_EOL .
          '       phpca -p path_to_php <directory to analyze>' . PHP_EOL . PHP_EOL;
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
        // Remove phpca's file name
        array_shift($arguments);

        $argument = array_shift($arguments);

        while (substr($argument, 0, 1) == '-') {

            switch ($argument) {

                case '-h':
                case '--help':
                    $this->printUsage();
                    exit();

                case '-p':
                    $this->phpExecutable = array_shift($arguments);
                    break;

                default:
                    throw new Exception('Unknown option: ' . $argument);
            }

            $argument = array_shift($arguments);
        }

        // the last argument is the file or directory name
        $this->path = $argument;
  }


  protected function checkSettings()
  {
      if ($this->path == '') {
          throw new Exception('Missing argument: no file or directory to analyze');
      }

      if ($this->phpExecutable == '') {
          throw new Exception('Missing argument: path to PHP executable (-p) must be specified');
      }
  }


  protected function doRun()
  {
    Constants::init();

    $linter = new Linter($this->phpExecutable);
    $linter->checkPhpBinary();

    $tokenizer = new Tokenizer();
    $rules = $this->loadRules();

    $this->result = new Result();

    $fileList = new FileList();

    foreach ($fileList->listFiles($this->path) as $file) {

      $this->result->addFile($file);

      $lintResult = $linter->check($file);

      if ($lintResult != '') {
        $this->printLetter('E');
        $this->result->addMessage(new LintError($file, strstr($lintResult, PHP_EOL, true)));
        continue;
      }

      $tokenizedFile = $tokenizer->tokenize($file, file_get_contents($file));

      foreach ($rules as $rule) {
        $tokenizedFile->rewind();
        $rule->check($tokenizedFile, $this->result);
      }

      if ($this->result->hasErrors($file)) {
        $this->printLetter('F');
      } else {
        $this->printLetter();
      }
    }
  }


  protected function printSummary()
  {
    echo PHP_EOL . PHP_EOL;

    if (!$this->result->hasErrors()) {
      echo 'OK';
    } else {

      foreach($this->result->getFiles() as $file) {
        if ($this->result->hasErrors($file)) {
          echo $file . ':' . PHP_EOL;
          foreach ($this->result->getErrors($file) as $error) {
            if ($error instanceOf LintError) {
              echo $error->getMessage() . PHP_EOL;
            } else {
              echo 'Line ' . $error->getLine() . ', column ' . $error->getColumn() . ': Error: ' . $error->getMessage() . PHP_EOL;
            }
          }
          echo PHP_EOL;
        }
      }

      echo 'FAIL';
    }

    echo ' (';
    echo $this->result->getNumberOfFiles() . ' files, ';
    echo $this->result->getNumberOfErrors() . ' errors, ';
    echo $this->result->getNumberOfWarnings() . ' warnings';
    echo ')';

    echo PHP_EOL . PHP_EOL;
  }


  public function run($arguments)
  {
    echo 'PHP Code Analyzer ' . $this->version . ' by Stefan Priebsch' . PHP_EOL . PHP_EOL;

    try {
      $this->parseCommandLine($arguments);
      $this->checkSettings();
      $this->doRun();
      $this->printSummary();
    }

    catch (Exception $e) {
      echo 'Error: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
      $this->printUsage();
      exit();
    }
  }
}
?>
