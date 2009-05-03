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

/**
 * The PHPCA application.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class Application
{
    /**
     * PHPCA version number
     *
     * @var string
     */
    static public $version = '0.2.5';

    /**
     * Path to the directory containing the rules
     *
     * @var string
     */
    protected $rulePath = 'PHPCA/Rules';

    /**
     * Result object
     *
     * @var Result
     */
    protected $result;

    protected $progressPrinter;

    /**
     * Register progress printer
     */
    public function registerProgressPrinter($progressPrinter)
    {
        if (!method_exists($progressPrinter, 'showProgress')) {
            throw new Exception('Progress printer does not have a showProgress() method');
        }

        $this->progressPrinter = $progressPrinter;
    }

    /**
     * Load the rules to check
     *
     * @return array $list List of Rule class names
     */
    public function loadRules($rulePath = null)
    {
        if (!is_null($rulePath)) {
            $this->rulePath = $rulePath;
        }

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

    /**
     * Main method
     *
     * @param string $phpExecutable path to PHP executable for lint check
     * @param string $path          path to file(s) to check
     * @return Result check result object
     */
    public function run($phpExecutable, $path)
    {
        if ($path == '') {
            throw new Exception('No file or directory to analyze');
        }

        if ($phpExecutable == '') {
            throw new Exception('No path to PHP executable specified');
        }

        Constants::init();

        $linter = new Linter($phpExecutable);
        $linter->checkPhpBinary();

        $tokenizer = new Tokenizer();
        $result    = new Result();
        $fileList  = new FileList();

        $rules = $this->loadRules();

        foreach ($fileList->listFiles($path) as $file) {

            $result->addFile($file);

            $lintResult = $linter->check($file);

            if ($lintResult == '') {
                $tokenizedFile = $tokenizer->tokenize($file, file_get_contents($file));

                foreach ($rules as $rule) {
                    $tokenizedFile->rewind();
                    $rule->check($tokenizedFile, $result);
                }

            } else {
                $result->addMessage(new LintError($file, strstr($lintResult, PHP_EOL, true)));
            }

            $this->progressPrinter->showProgress($file, $result);
        }

        return $result;
    }
}
?>
