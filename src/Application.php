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
 * The PHPCA application.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Application
{
    /**
     * @var string
     */
    static public $version = '0.3.0';

    /**
     * @var array of string
     */
    protected $rulePaths = array();

    /**
     * @var array of Rule
     */
    protected $rules = array();

    /**
     * @var array of string
     */
    protected $disabledRuleNames = array();

    /**
     * @var Result
     */
    protected $result;

    /**
     * @var ProgressPrinterInterface
     */
    protected $progressPrinter;

    /**
     * @var bool
     */
    protected $enableBuiltInRules = true;

    /**
     * @var int
     */
    protected $numberOfFiles = 0;

    /**
     * Returns array of all .php filenames in given directory.
     * If $path points to a single file, we do not iterate.
     *
     * @param string $path
     * @return array
     */
    protected function listFiles($path)
    {
        if (is_file($path)) {
            return array($path);
        }

        $result = array();

        if (!file_exists($path)) {
            throw new Exception($path . ' not found');
        }

        $it = new PhpFileFilterIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)));

        foreach ($it as $file) {
            $result[] = $file->getPathname();
        }

        return $result;
    }

    /**
     * Converts a rule filename to a rule classname by removing .php
     *
     * @param string $fileName The file name
     * @return string The rule name
     */
    protected function toClassName($fileName)
    {
        return '\\spriebsch\\PHPca\\Rule\\' . substr(basename($fileName), 0, -4);
    }

    /**
     * Load all rules from given directories
     * Note: built-in rules are listed in the $_ClassMap.
     * Returns an array of Rule object instances.
     *
     * @return array
     * @todo handle potential class name conflicts between built-in and custom rules
     */
    protected function loadRules()
    {
        $result = array();

        if ($this->enableBuiltInRules) {

            $builtInRules = $this->listFiles(__DIR__ . '/Rule');

            foreach ($builtInRules as $rule) {
                $className = $this->toClassName($rule);

                if (!in_array($className, $this->disabledRuleNames)) {
                    $result[] = new $className;
                }
            }
        }

        foreach ($this->rulePaths as $path) {

            $rules = $this->listFiles($path);

            foreach($rules as $rule) {
                $className = $this->toClassName($rule);

                if (!in_array($className, $this->disabledRuleNames)) {
                    require_once $rule;
                    $result[] = new $className;
                }
            }
        }

        return $result;
    }

    /**
     * Enforce all rules
     *
     * @param string $fileName Filename
     * @param File $file
     * @return void
     */
    protected function enforceRules($fileName, File $file)
    {
        if (sizeof($this->rules) == 0) {
            throw new Exception('No rules to enforce');
        }

        foreach ($this->rules as $rule) {
            try {
                $rule->check($file, $this->result);
            }

            catch (Exception $e) {
                $this->result->addMessage(new RuleError($fileName, 'Rule ' . get_class($rule) . ': ' . $e->getMessage()));
            }
        }
    }

    /**
     * Disable a rule
     *
     * @param string $name Rule Name
     */
    public function disableRule($name)
    {
        $this->disabledRuleNames[] = $name;
    }

    /**
     * Toggle use of built-in rules.
     * Default value is true.
     *
     * @param bool $flag Flag
     * @return void
     */
    public function setEnableBuiltInRules($flag)
    {
        if (!is_bool($flag)) {
            throw new Exception('Boolean value expected');
        }

        $this->enableBuiltInRules = $flag;
    }

    /**
     * Add a path to load rules from.
     *
     * @param string $path
     * @return array
     */
    public function addRulePath($path)
    {
        if (!file_exists($path)) {
            throw new Exception('The path ' . $path . ' does not exist');
        }

        // Make sure each item of the rule path is unique
        if (!in_array($path, $this->rulePaths)) {
            $this->rulePaths[] = $path;
        }
    }

    /**
     * Return an array of Rule types.
     *
     * @return array
     */
    public function getRulePaths()
    {
        return $this->rulePaths;
    }

    /**
     * Register a callback that is notified whenever a file has been processed.
     * Can be used to display a dot, E of F for each processed file in console mode.
     *
     * @param object
     * @return void
     */
    public function registerProgressPrinter(ProgressPrinterInterface $progressPrinter)
    {
        $this->progressPrinter = $progressPrinter;
    }

    /**
     * Returns the number of files to analyze.
     * The value will not be available before run() has been called
     * and should only be used by the progress printer.
     *
     * @return int
     */
    public function getNumberOfFiles()
    {
        return $this->numberOfFiles;
    }

    /**
     * PHPca's main method. Returns a result object holding
     * error and warning messages for all the files that have been analyzed.
     *
     * @param string $pathToPhpExecutable path to PHP executable for lint check
     * @param string $fileOrDirectory     path to file or directory to check
     * @return object
     */
    public function run($pathToPhpExecutable, $fileOrDirectory)
    {
        if ($pathToPhpExecutable == '') {
            throw new Exception('No path to PHP executable specified');
        }

        if ($fileOrDirectory == '') {
            throw new Exception('No file or directory to analyze');
        }

        // Define our own additionl T_* token constants
        Constants::init();

        // Set up the lint checker and make sure that given path points to a PHP binary
        $linter = new Linter($pathToPhpExecutable);

        // Create result object that collects the error and warning messages
        $this->result = new Result();

        // Create a list of all rules to enforce
        $this->rules = $this->loadRules();

        // List all PHP files in given path
        $phpFiles = $this->listFiles($fileOrDirectory);

        if (sizeof($phpFiles) == 0) {
            throw new Exception('No PHP files to analyze');
        }

        $this->numberOfFiles = sizeof($phpFiles);

        foreach ($phpFiles as $phpFile) {

            // Remember that we have processed this file,
            // even if it generates no message at all.
            $this->result->addFile($phpFile);

            if ($linter->runLintCheck($phpFile)) {
                $this->enforceRules($phpFile, Tokenizer::tokenize($phpFile, file_get_contents($phpFile)));
            } else {
                $this->result->addMessage(new LintError($phpFile, $linter->getErrorMessages()));
            }

            // Notify the progress printer that we have analyzed a file
            if (is_object($this->progressPrinter)) {
                $this->progressPrinter->showProgress($phpFile, $this->result, $this);
            }
        }

        // Return the result object containing all error and warning messages
        return $this->result;
    }
}
?>