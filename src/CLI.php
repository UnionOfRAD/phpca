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
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\PHPca;

/**
 * Command line interface of PHPca.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class CLI implements ProgressPrinterInterface
{
    /**
     * Path or file to analyze
     *
     * @var string
     */
    protected $path;

    /**
     * Path to PHP executable
     *
     * @var string
     */
    protected $phpExecutable;

    /**
     * Position counter for dot output of progress printer
     *
     * @var integer
     */
    protected $positionCount = 0;

    /**
     * File counter for dot output of progress printer
     *
     * @var integer
     */
    protected $fileCount = 0;

    protected $startTime = 0;
    protected $endTime = 0;

    protected $numberOfFiles = 0;

    /**
     * Print usage message
     *
     * @return void
     */
    protected function printUsage()
    {
        print 'Usage: phpca -p path_to_php <file to analyze>' . PHP_EOL .
              '       phpca -p path_to_php <directory to analyze>' . PHP_EOL . PHP_EOL;
    }

    /**
     * Get letter to display progress (having analyzed one file),
     * E on lint error, F for failed checks, . for successful check
     *
     * @param string $file File that was checked
     * @param Result $result Result object
     * @return string
     */
    protected function getProgressLetter($file, Result $result)
    {
        if ($result->hasLintError($file)) {
            return 'E';
        }

        if ($result->hasErrors($file)) {
            return 'F';
        }

        return '.';
    }

    /**
     * Parse the command line.
     *
     * @param array $arguments $argv
     * @return void
     */
    protected function parseCommandLine($arguments)
    {
        // Remove $argv[0], phpca's file name
        array_shift($arguments);

        $argument = array_shift($arguments);

        // parse command line parameters starting with - (which implies --)
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

        // Last argument is the file or directory name of the files to check
        $this->path = $argument;
    }

    protected function startTimer()
    {
        $this->startTime = microtime(true);
    }

    protected function endTimer()
    {
        $this->endTime = microtime(true);
    }

    /**
     * Print the test summary
     *
     * @return void
     */
    protected function printSummary()
    {
        echo PHP_EOL . PHP_EOL;

        $time = ceil($this->endTime - $this->startTime);

        echo 'Time: ' . $time . ' ';
        if ($time == 1) {
            echo 'second';
        } else {
            echo 'seconds';
        }
        echo PHP_EOL . PHP_EOL;

        if (!$this->result->hasErrors()) {
            echo 'OK';
        } else {
            foreach($this->result->getFiles() as $file) {
                if ($this->result->hasErrors($file)) {
                    echo $file . ':' . PHP_EOL;
                    foreach ($this->result->getErrors($file) as $error) {
                        if ($error instanceOf LintError) {
                            // For lint errors, display the original error message
                            echo $error->getMessage() . PHP_EOL;
                        } else {
                            // A "line number | column number | message" line
                            echo sprintf('%4u', $error->getLine()) . '|' . sprintf('%3u', $error->getColumn()) . '| ' . $error->getMessage() . PHP_EOL;
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

    /**
     * Run PHPCA
     *
     * @param array $arguments $argv
     */
    public function run($arguments)
    {
        echo 'PHP Code Analyzer ' . Application::$version . ' by Stefan Priebsch.' . PHP_EOL . PHP_EOL;

        try {
            $this->startTimer();

            $this->parseCommandLine($arguments);

            $application = new Application();
            $application->registerProgressPrinter($this);

            $this->result = $application->run($this->phpExecutable, $this->path);

            $this->endTimer();

            $this->printSummary();
        }

        catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
            $this->printUsage();
            exit(-1);
        }
    }

    /**
     * Prepend spaces to file counter to align it with its maximum possible
     * value which equals $this->numberOfFiles
     *
     * @param string $fileCount
     */
    protected function formatFileCount($fileCount)
    {
        $numberOfFilesLength = strlen($this->numberOfFiles);
        $fileCountLength = strlen($this->fileCount);

        if ($numberOfFilesLength > $fileCountLength) {
            $fileCount = str_pad($fileCount, $numberOfFilesLength, ' ', STR_PAD_LEFT);
        }

        return $fileCount;
    }

    /**
     * Print one character representing a checked file. Defaults to dot.
     *
     * @param string $file File that was checked
     * @param Result $result Result object
     * @return void
     */
    public function showProgress($file, Result $result, Application $application)
    {
        // we just fetch the number of files from the application once,
        // since it is not going to change during the run.
        // Note: if number of files is zero, an exception is thrown in run(),
        if ($this->numberOfFiles == 0) {
            $this->numberOfFiles = $application->getNumberOfFiles();
        }

        $this->fileCount++;
        $this->positionCount++;

        echo $this->getProgressLetter($file, $result);

        if ($this->positionCount > 59) {
            echo ' ' . $this->formatFileCount($this->fileCount) . ' / ' . $this->numberOfFiles . PHP_EOL;
            $this->positionCount = 0;
        }
    }
}
?>