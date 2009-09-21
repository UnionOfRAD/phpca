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
 * Collects the message objects that represent rule errors, lint errors,
 * and rule violations.
 * Also holds a list of processed files to allow counting number of checked files.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Result
{
    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @var array
     */
    protected $namespaces = array();

    /**
     * @var array
     */
    protected $classes = array();

    /**
     * @var array
     */
    protected $functions = array();

    /**
     * @var array
     */
    protected $violationCount = array();

    /**
     * @var int
     */
    protected $globalViolationCount = 0;

    /**
     * @var int
     */
    protected $globalLintErrorCount = 0;

    /**
     * @var int
     */
    protected $globalRuleErrorCount = 0;

    /**
     * Helper function for sorting results by line number.
     * Compares line numbers of two Message objects. If they
     * are equal, compares column numbers.
     *
     * @param Message $t1
     * @param Message $t2
     * @return int
     * @todo fix the two tokens in one column problem
     */
    protected function sortByLine($t1, $t2)
    {
        $l1 = $t1->getLine();
        $l2 = $t2->getLine();

        // When line numbers match, sort by column
        if ($l1 == $l2) {
            $c1 = $t1->getColumn();
            $c2 = $t2->getColumn();

            // Two tokens can't be on the same line AND column.
            // @codeCoverageIgnoreStart
            if ($c1 == $c2) {
                return 1;
//                 throw new Exception('Cannot sort two tokens that are on the same column');
            }
            // @codeCoverageIgnoreEnd

            return ($c1 > $c2) ? 1 : -1;
        }

        return ($l1 > $l2) ? 1 : -1;
    }

    /**
     * Add a processed file.
     * We just use the file name here to reduce coupling to the File class,
     * and also because there is no File instance when a LintError has occured.
     *
     * @param string $file filename
     * @return null
     */
    public function addFile($file)
    {
        $this->files[] = $file;
    }

    /**
     * Returns array of all processed files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add namespaces to the result.
     *
     * @param File $file
     * @param array $namespaces
     * @return null
     */
    public function addNamespaces($file, array $namespaces)
    {
        if (!isset($this->namespaces[$file])) {
            $this->namespaces[$file] = $namespaces;
        } else {
            $this->namespaces[$file] = array_merge($this->namespaces[$file], $namespaces);
        }
    }

    /**
     * Returns the namespaces that are part of the given file.
     *
     * @param string $file
     * @return array
     */
    public function getNamespaces($file)
    {
        if (!isset($this->namespaces[$file])) {
            return array();
        }

        return $this->namespaces[$file];
    }

    /**
     * Adds classes to the result.
     *
     * @param string $file
     * @param array $classes
     * @return null
     */
    public function addClasses($file, array $classes)
    {
        if (!isset($this->classes[$file])) {
            $this->classes[$file] = $classes;
        } else {
            $this->classes[$file] = array_merge($this->classes[$file], $classes);
        }
    }

    /**
     * Returns the classes in a given file
     *
     * @param string $file
     * @return null
     */
    public function getClasses($file)
    {
        return $this->classes[$file];
    }

    /**
     * Adds functions to the result.
     *
     * @param string $file
     * @param array $functions
     * @return null
     */
    public function addFunctions($file, array $functions)
    {
        if (!isset($this->functions[$file])) {
            $this->functions[$file] = $functions;
        } else {
            $this->functions[$file] = array_merge($this->functions[$file], $functions);
        }
    }

    /**
     * Returns the number of files that have alreday been processed.
     *
     * @return int
     */
    public function getNumberOfFiles()
    {
        return count($this->files);
    }

    /**
     * Add a Messsage
     *
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        // Flag to make sure that each message is only counted once globally
        $counted = false;

        $filename = $message->getFileName();

        if ($this->hasLintError($filename)) {
            throw new Exception('File ' . $filename . ' alread has a lint error');
        }

        $this->messages[$filename][] = $message;

        if ($message instanceOf RuleError) {
            $this->globalRuleErrorCount++;
            $counted = true;
        }

        if ($message instanceOf LintError) {
            $this->globalLintErrorCount++;
            $counted = true;
        }

        if ($message instanceOf Violation) {
            if (!$counted) {
                $this->globalViolationCount++;
            }

            if (!isset($this->violationCount[$filename])) {
                $this->violationCount[$filename] = 1;
            } else {
                $this->violationCount[$filename]++;
            }
        }
    }

    /**
     * Checks whether there are any errors, or errors for given filename.
     *
     * @param string $file filename
     * @return bool
     */
    public function hasErrors($file = null)
    {
        if (is_null($file)) {
            return $this->globalViolationCount > 0 || $this->globalLintErrorCount > 0 || $this->globalRuleErrorCount > 0;
        }

        return $this->hasViolations($file) || $this->hasLintError($file) || $this->hasRuleError($file);
    }

    /**
     * Checks whether there are any violations, or violations for given filename.
     *
     * @param string $file filename
     * @return bool
     */
    public function hasViolations($file = null)
    {
        if (is_null($file)) {
            return $this->globalViolationCount > 0 || $this->globalLintErrorCount > 0 || $this->globalRuleErrorCount > 0;
        }

        if (!isset($this->violationCount[$file])) {
            return false;
        }

        return $this->violationCount[$file] > 0;
    }

    /**
     * Returns the number of rule violations.
     * 
     * @return int
     */
    public function getNumberOfViolations()
    {
        return $this->globalViolationCount;
    }

    /**
     * Returns the number of lint errors.
     *
     * @return int
     */
    public function getNumberOfLintErrors()
    {
        return $this->globalLintErrorCount;
    }

    /**
     * Returns the number of rule errors (exceptions that occured inside a Rule).
     *
     * @return int
     */
    public function getNumberOfRuleErrors()
    {
        return $this->globalRuleErrorCount;
    }

    /**
     * Returns whether given file has a lint error.
     *
     * @param string $file
     * @return bool
     */
    public function hasLintError($file)
    {
        if (!isset($this->messages[$file])) {
            return false;
        }

        foreach ($this->messages[$file] as $message) {
            if ($message instanceOf LintError) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether given file has a rule error.
     *
     * @param string $file
     * @return bool
     */
    public function hasRuleError($file)
    {
        if (!isset($this->messages[$file])) {
            return false;
        }

        foreach ($this->messages[$file] as $message) {
            if ($message instanceOf RuleError) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the lint error for given file.
     *
     * @param string $file
     * @return array
     */
    public function getLintError($file)
    {
        if (isset($this->messages[$file])) {
            foreach ($this->messages[$file] as $message) {
                if ($message instanceOf LintError) {
                    return $message;
                }
            }
        }

        throw new Exception('File ' . $file . ' has no lint error');
    }

    /**
     * Return array with all violations, or all violations for given filename.
     *
     * @param string $file
     * @return array
     */
    public function getViolations($file)
    {
        $result = array();

        if (!isset($this->messages[$file])) {
            return array();
        }

        foreach ($this->messages[$file] as $message) {
            if ($message instanceOf Violation) {
                $result[] = $message;
            }
        }

        usort($result, array($this, 'sortByLine'));

        return $result;
    }
}
?>