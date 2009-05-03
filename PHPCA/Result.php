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

class Result
{
    protected $files = array();
    protected $messages = array();

    protected $errorCount = array();
    protected $warningCount = array();

    protected $globalErrorCount = 0;
    protected $globalWarningCount = 0;

    public function addFile($file)
    {
        $this->files[] = $file;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getNumberOfFiles()
    {
        return count($this->files);
    }

    public function addMessage(Message $message)
    {
        $filename = $message->getFileName();
        $this->messages[$filename][] = $message;

        if ($message instanceOf Error) {
            $this->globalErrorCount++;

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

    public function getNumberOfWarnings()
    {
        return $this->globalWarningCount;
    }

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

    public function hasErrors($file = null)
    {
        if (is_null($file)) {
            return $this->globalErrorCount > 0;
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

        return $result;
    }
}
?>
