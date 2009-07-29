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

    /**
     * Start time of the analysis
     *
     * @var float
     */
    protected $startTime = 0;

    /**
     * End time of the analysis
     *
     * @var float
     */
    protected $endTime = 0;

    /**
     * Number of files that will be analyzed.
     * See Application::getNumberOfFiles().
     *
     * @var int
     */
    protected $numberOfFiles = 0;

    /**
     * Whether to exit on errors during file analysis.
     *
     * @var bool
     */
    protected $exitOnError = true;

    /**
     * Prints the usage message.
     *
     * @return void
     */
    protected function printUsageMessage()
    {
        echo 'Usage: php phpca.phar -p <file> <file to analyze>' . PHP_EOL .
             '       php phpca.phar -p <file> <directory to analyze>' . PHP_EOL . PHP_EOL .
             '  -p <file>' . PHP_EOL .
             '  --php <file>      Specify path to PHP executable (required).' . PHP_EOL . PHP_EOL .
             '  -l' . PHP_EOL .
             '  --list            List all built-in rules.' . PHP_EOL . PHP_EOL .
             '  -h' . PHP_EOL .
             '  --help            Prints this usage information.' . PHP_EOL . PHP_EOL .
             '  -v' . PHP_EOL .
             '  --version         Prints the version number.' . PHP_EOL . PHP_EOL;
    }

    /**
     * Converts rule file name to display rule name.
     *
     * @param string $fileName
     * @return string
     */
    protected function toRuleName($fileName)
    {
        $result = basename($fileName);
        $result = substr($result, 0, -4);
        $result = str_replace('Rule', '', $result);

        $result = preg_replace('/([A-Z])/', ' $0', $result);

        return $result;
    }

    /**
     * Lists all built-in rules.
     *
     * @return Result
     */
    protected function printBuiltInRulesCommand()
    {
        $application = new Application();
        $rules = $application->listFiles(__DIR__ . '/Rule');
        sort($rules);

        echo 'Built-in rules: ' . PHP_EOL . PHP_EOL;

        foreach ($rules as $rule) {
            echo '  -' . $this->toRuleName($rule) . ' (' . basename($rule) . ')'. PHP_EOL;
        }

        echo PHP_EOL;

        return new Result();
    }

    /**
     * Prints version number.
     *
     * @return Result
     */
    protected function printVersionCommand()
    {
        echo 'Version: ' . Application::$version . PHP_EOL . PHP_EOL;
        return new Result();
    }

    /**
     * Prints usage message.
     *
     * @return Result
     */
    protected function printUsageCommand()
    {
        $this->printUsageMessage();
        return new Result();
    }

    /**
     * Anaylzes PHP files by calling Application::run().
     *
     * @return void
     */
    protected function analyzeFilesCommand()
    {
        $application = new Application();
        $application->registerProgressPrinter($this);

        return call_user_func_array(array($application, 'run'), array($this->phpExecutable, $this->path));
    }

    /**
     * Get letter to display progress (having analyzed one file),
     * E on lint error, F for failed checks, . for successful check
     *
     * @param string $file File that was analyzed
     * @param Result $result Result object
     * @return string
     */
    protected function getProgressLetter($file, Result $result)
    {
        if ($result->hasLintError($file)) {
            return 'L';
        }

        if ($result->hasRuleError($file)) {
            return 'R';
        }

        if ($result->hasErrors($file)) {
            return 'E';
        }

        // a warning does not count as error, thus display a dot
        if ($result->hasWarnings($file)) {
            return '.';
        }

        return '.';
    }

    /**
     * Parse the command line and determine which command method to run.
     * Returns the name of the method to run.
     *
     * @param array $arguments Command line arguments
     * @return string
     */
    protected function parseCommandLine($arguments)
    {
        $method = 'analyzeFilesCommand';

        // Remove $argv[0], phpca's file name
        array_shift($arguments);

        $argument = array_shift($arguments);

        // parse command line parameters starting with - (which implies --)
        while (substr($argument, 0, 1) == '-') {

            switch ($argument) {

                case '-h':
                case '--help':
                    $method = 'printUsageCommand';
                    break;

                case '-l':
                case '--list':
                    $method = 'printBuiltInRulesCommand';
                    break;

                case '-p':
                case '--php':
                    $this->phpExecutable = array_shift($arguments);
                    break;

                case '-v':
                case '--version':
                    $method = 'printVersionCommand';
                    break;


                default:
                    throw new Exception('Unknown option: ' . $argument);
            }

            $argument = array_shift($arguments);
        }

        // Last argument is the file or directory name of the files to check
        $this->path = $argument;

        return $method;
    }

    /**
     * Starts the timer.
     *
     * @return void
     */
    protected function startTimer()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Stops the timer.
     *
     * @return void
     */
    protected function endTimer()
    {
        $this->endTime = microtime(true);
    }

    /**
     * Prepend spaces to file counter to align it with its maximum possible
     * value which equals $this->numberOfFiles. Used by showProgress().
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
     * Wrapper method to exit() with a given exit code.
     * Will only exit when $exitOnError is true.
     * This function makes it possible to unit test the CLI class,
     * since exit'ing will also exit from PHPUnit.
     *
     * @param int $exitCode
     */
    protected function doExit($exitCode)
    {
        // @codeCoverageIgnoreStart
        if ($this->exitOnError) {
            exit($exitCode);
        }
        // @codeCoverageIgnoreEnd
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

        // only display lint errors in statistics if they occured
        if ($this->result->getNumberOfLintErrors() > 0) {
            echo $this->result->getNumberOfLintErrors() . ' lint errors [L], ';
        }

        // only display rule errors in statistics if they occured
        if ($this->result->getNumberOfRuleErrors() > 0) {
            echo $this->result->getNumberOfRuleErrors() . ' rule errors [R], ';
        }

        echo $this->result->getNumberOfErrors() . ' errors [E], ';
        echo $this->result->getNumberOfWarnings() . ' warnings';
        echo ')';

        echo PHP_EOL . PHP_EOL;
    }

    /**
     * Run PHPCA. Parses the command line and executes the selected command.
     *
     * @param array $arguments $argv
     */
    public function run($arguments)
    {
        echo 'PHP Code Analyzer ' . Application::$version . ' by Stefan Priebsch.' . PHP_EOL . PHP_EOL;

        try {
            $this->startTimer();

            $method = $this->parseCommandLine($arguments);
            $this->result = $this->$method();

            $this->endTimer();

            // Only print the summary when we actually analyzed files
            if ($method == 'analyzeFilesCommand') {
                $this->printSummary();
            }

            if ($this->result->hasErrors()) {
                $this->doExit(-1);
            }
        }

        catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
            $this->printUsageMessage();
            $this->doExit(-1);
        }
    }

    /**
     * Print one character representing a checked file. Defaults to dot.
     * When a multiple of 60 files has been analyzed, the number of
     * files that have already been analyzed and the total number of files
     * to analyze plus a line break will be output.
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