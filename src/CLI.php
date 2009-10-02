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
     * Configuration
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * The coding standard to be used.
     *
     * @var string
     */
    protected $codingStandard;

    /**
     * Settings array parsed from configuration ini file
     *
     * @var array
     */
    protected $iniSettings = array();

    /**
     * Settings array parsed from standards ini file
     *
     * @var array
     */
    protected $standardSettings = array();

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
     * @var bool
     */
    protected $printStatistics = false;

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * Constructs the object.
     *
     * @return null
     */
    public function __construct()
    {
        $this->configuration = new Configuration();
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
     * Loads the configuration file
     *
     * @param string $file
     */
    protected function loadConfigurationFile($file)
    {
        if (!file_exists($file)) {
            throw new Exception('Configuration file ' . $file . ' not found');
        }

        $this->iniSettings = parse_ini_file($file, true);
    }

    /**
     * Loads coding standard
     *
     * @param string $file
     */
    protected function loadCodingStandard($standard)
    {
        $filename = __DIR__ . '/Standard/' . $standard . '.ini';

        if (!file_exists($filename)) {
            throw new Exception('Coding standard ' . $standard . ' not found in ' . $filename);
        }

        $this->standardSettings = parse_ini_file($filename, true);
    }

    /**
     * Prints the usage message.
     *
     * @return void
     */
    protected function printUsageMessage()
    {
        print 'Usage: php phpca.phar -p <file> <file to analyze>' . PHP_EOL .
              '       php phpca.phar -p <file> <directory to analyze>' . PHP_EOL . PHP_EOL .

              '  -h' . PHP_EOL .
              '  --help              Prints this usage information.' . PHP_EOL . PHP_EOL .

              '  -i' . PHP_EOL .
              '  --info              Prints information about coding standards and rules.' . PHP_EOL . PHP_EOL .

              '  -p <file>' . PHP_EOL .
              '  --php <file>        Specify path to PHP executable (required).' . PHP_EOL . PHP_EOL .

              '  -r <rules>' . PHP_EOL .
              '  --rules <rules>     Specify file rules to analyze, without Rule end.' . PHP_EOL .
              '                      Separate multiple entries by comma, without whitespace.' . PHP_EOL .             '                      If not specified, all rules will be executed.' . PHP_EOL . PHP_EOL .

              '  -s' . PHP_EOL .
              '  --standard          The coding standard to use (defaults to thePHP.cc).' . PHP_EOL . PHP_EOL .

              '  -v' . PHP_EOL .
              '  --verbose           Verbose output.' . PHP_EOL . PHP_EOL;
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
     * Prints out info.
     *
     * @return Result
     */
    protected function printInfoCommand()
    {
        $this->printStandards();
        $this->printBuiltInRules();

        return new Result();
    }

    /**
     * Lists all available coding standards.
     *
     * @return Result
     */
    protected function printStandards()
    {
        $application = new Application();
        $standards = $application->listFiles(__DIR__ . '/Standard', array('ini'));
        sort($standards);

        print 'Available coding standards: ' . PHP_EOL . PHP_EOL;

        foreach ($standards as $standard) {
            print '  - ' . str_replace('.ini', '', basename($standard)) . ' (' . basename($standard) . ')'. PHP_EOL;
        }

        print PHP_EOL;
    }

    /**
     * Lists all built-in rules.
     *
     * @return Result
     */
    protected function printBuiltInRules()
    {
        $application = new Application();
        $rules = $application->listFiles(__DIR__ . '/Rule');
        sort($rules);

        print 'Available rules: ' . PHP_EOL . PHP_EOL;

        foreach ($rules as $rule) {
            print '  -' . $this->toRuleName($rule) . ' (' . basename($rule) . ')'. PHP_EOL;
        }

        print PHP_EOL;
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

        $this->configuration->setStandard($this->standardSettings);
        $this->configuration->setConfiguration($this->iniSettings);

        return call_user_func_array(array($application, 'run'), array($this->phpExecutable, $this->path, $this->configuration));
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
            return 'E';
        }

        if ($result->hasRuleError($file)) {
            return 'E';
        }

        if ($result->hasViolations($file)) {
            return 'F';
        }

        return '.';
    }

    /**
     * Make sure that $switch has an argument that does not start with - or --.
     *
     * @param string $switch
     * @param array $arguments
     * @return null
     * @throws Exception
     */
    protected function checkNextArgument($switch, array $arguments)
    {
        if (!isset($arguments[0]) || substr($arguments[0], 0, 1) == '-') {
            throw new Exception('Missing parameter to ' . $switch . ' switch');
        }
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

                case '-c':
                case '--config':
                    $this->checkNextArgument($argument, $arguments);
                    $this->loadConfigurationFile(array_shift($arguments));
                    break;

                case '-h':
                case '--help':
                    $method = 'printUsageCommand';
                    break;

                case '-i':
                case '--info':
                    $method = 'printInfoCommand';
                    break;

                case '-p':
                case '--php':
                    $this->checkNextArgument($argument, $arguments);
                    $this->phpExecutable = array_shift($arguments);
                    break;

                case '-p':
                case '--php':
                    $this->checkNextArgument($argument, $arguments);
                    $this->phpExecutable = array_shift($arguments);
                    break;

                case '-r':
                case '--rules':
                    $this->checkNextArgument($argument, $arguments);
                    $this->configuration->setRules(explode(',', array_shift($arguments)));
                    break;

                case '-v':
                case '--verbose':
                    $this->verbose = true;
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
     * Print the test summary
     *
     * @return void
     */
    protected function printSummary()
    {
        print PHP_EOL . PHP_EOL;

        $time = ceil($this->endTime - $this->startTime);

        print 'Time: ' . $time . ' ';
        if ($time == 1) {
            print 'second';
        } else {
            print 'seconds';
        }
        print PHP_EOL . PHP_EOL;

        if (!$this->result->hasErrors()) {
            print 'OK';
        } else {
            foreach($this->result->getFiles() as $file) {
                if ($this->result->hasErrors($file)) {
                    print $file . ':' . PHP_EOL;
                    if ($this->result->hasLintError($file)) {
                        // For lint errors, display original error message
                        print $this->result->getLintError($file)->getMessage() . PHP_EOL;
                    }

                    foreach ($this->result->getViolations($file) as $error) {
                        // A "line number | column number | message" line
                        print sprintf('%4u', $error->getLine()) . '|' . sprintf('%3u', $error->getColumn()) . '| ' . $error->getMessage() . PHP_EOL;
                    }
                    print PHP_EOL;
                }
            }

            print 'FAIL';
        }

        print ' (';
        print $this->result->getNumberOfFiles() . ' files, ';

        // only display lint errors in statistics if they occured
        if ($this->result->getNumberOfLintErrors() > 0) {
            print $this->result->getNumberOfLintErrors() . ' lint errors, ';
        }

        // only display rule errors in statistics if they occured
        if ($this->result->getNumberOfRuleErrors() > 0) {
            print $this->result->getNumberOfRuleErrors() . ' rule errors, ';
        }

        print $this->result->getNumberOfViolations() . ' violations';
        print ')';

        print PHP_EOL . PHP_EOL;
    }

    /**
     * Run PHPCA. Parses the command line and executes the selected command.
     *
     * @param array $arguments $argv
     */
    public function run($arguments)
    {
        print 'PHP Code Analyzer ' . Application::$version . ' by Stefan Priebsch.' . PHP_EOL . PHP_EOL;

        try {
            $this->startTimer();

            $method = $this->parseCommandLine($arguments);

            $rules = $this->configuration->getRules();

            if (sizeof($rules) != 0) {
                print 'Rules: ' . implode(', ', $rules) . PHP_EOL . PHP_EOL;
            }

            $this->result = $this->$method();

            $this->endTimer();

            // Only print the summary when we actually analyzed files
            if ($method == 'analyzeFilesCommand') {
                $this->printSummary();
                if ($this->printStatistics) {
                    $this->printStatistics();
                }
            }

            if ($this->result->hasErrors()) {
                $this->doExit(-1);
            }
        }

        catch (Exception $e) {
            print 'Error: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
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

        $letter = $this->getProgressLetter($file, $result);

        if ($this->verbose) {
            switch ($letter) {
                case '.':
                    $letter = 'OK';
                    break;
                case 'F':
                    $letter = 'FAIL';
                    break;
                case 'E':
                    $letter = 'ERROR';
                    break;
            }

            print $this->formatFileCount($this->fileCount) . ' ' . $file . ': ' . $letter . PHP_EOL;
        } else {
            print $letter;

            if ($this->positionCount > 59) {
                print ' ' . $this->formatFileCount($this->fileCount) . ' / ' . $this->numberOfFiles . PHP_EOL;
                $this->positionCount = 0;
            }            
        }
    }
}
?>