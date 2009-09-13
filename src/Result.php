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
 * Result
 * Collects the linterror, error, and message objects that represent
 * the results of a lint check.
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
    protected $errorCount = array();

    /**
     * @var array
     */
    protected $warningCount = array();

    /**
     * @var int
     */
    protected $globalErrorCount = 0;

    /**
     * @var int
     */
    protected $globalLintErrorCount = 0;

    /**
     * @var int
     */
    protected $globalRuleErrorCount = 0;

    /**
     * @var int
     */
    protected $globalWarningCount = 0;

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
     * @return void
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
        // Flag to make sure that each error is only counted once globally
        $counted = false;

        $filename = $message->getFileName();
        $this->messages[$filename][] = $message;

        if ($message instanceOf RuleError) {
            $this->globalRuleErrorCount++;
            $counted = true;
        }

        if ($message instanceOf LintError) {
            $this->globalLintErrorCount++;
            $counted = true;
        }

        if ($message instanceOf Error) {
            if (!$counted) {
                $this->globalErrorCount++;
            }

            if (!isset($this->errorCount[$filename])) {
                $this->errorCount[$filename] = 1;
            } else {
                $this->errorCount[$filename]++;
            }
        }

        if ($message instanceOf Warning) {
            $this->globalWarningCount++;

            if (!isset($this->warningCount[$filename])) {
                $this->warningCount[$filename] = 1;
            } else {
              $this->warningCount[$filename]++;
            }
        }
    }

    /**
     * Checks whether there are any warnings, or warnings for given filename.
     *
     * @param string $file filename
     * @return bool
     */
    public function hasWarnings($file = null)
    {
        if (is_null($file)) {
            return $this->globalWarningCount > 0;
        }

        if (!isset($this->warningCount[$file])) {
            return false;
        }

        return $this->warningCount[$file] > 0;
    }

    /**
     * Return number of warnings.
     *
     * @return int
     */
    public function getNumberOfWarnings()
    {
        return $this->globalWarningCount;
    }

    /**
     * Return array with all warnings, or all warnings for given filename.
     *
     * @param string $file
     * @return array
     */
    public function getWarnings($file)
    {
        $result = array();

        if (!isset($this->messages[$file])) {
            return array();
        }

        foreach ($this->messages[$file] as $message) {
            if ($message instanceOf Warning) {
                $result[] = $message;
            }
        }

        return $result;
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
            return $this->globalErrorCount > 0 || $this->globalLintErrorCount > 0 || $this->globalRuleErrorCount > 0;
        }

        if (!isset($this->errorCount[$file])) {
            return false;
        }

        return $this->errorCount[$file] > 0;
    }

    public function getNumberOfErrors()
    {
        return $this->globalErrorCount;
    }

    public function getNumberOfLintErrors()
    {
        return $this->globalLintErrorCount;
    }

    public function getNumberOfRuleErrors()
    {
        return $this->globalRuleErrorCount;
    }
    
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
     * Return array with all errors, or all errors for given filename.
     *
     * @param string $file
     * @return array
     */
    public function getErrors($file)
    {
        $result = array();

        if (!isset($this->messages[$file])) {
            return array();
        }

        foreach ($this->messages[$file] as $message) {
            if ($message instanceOf Error) {
                $result[] = $message;
            }
        }

        usort($result, array($this, 'sortByLine'));

        return $result;
    }
}
?>
