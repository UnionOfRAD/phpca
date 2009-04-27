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

class Linter
{
    /**
     * @var string
     */
    protected $phpExecutable;


    /**
     * Constructs the object
     *
     * @param string $phpExecutable Path to PHP executable 
     */
    public function __construct($phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;
    }


    /**
     * Run the lint check using given PHP executable.
     * Returns empty string on success, or error message on failure.
     *
     * @param string $file Path to the file to lint
     * @return string
     * @throws RuntimeException PHP excutable ... not found
     * @throws RuntimeException PHP excutable ... not executable
     * @throws RuntimeException Executable ... is not a PHP executable
     */
    public function checkPhpBinary()
    {
        if (!file_exists($this->phpExecutable)) {
            throw new \RuntimeException('PHP executable ' . $this->phpExecutable . ' not found');
        }

        if (!is_executable($this->phpExecutable)) {
            throw new \RuntimeException('PHP executable ' . $this->phpExecutable . ' not executable');
        }

        $cmd = $this->phpExecutable . ' -v 2>/dev/null';
        $output = trim(shell_exec($cmd));

        if (substr($output, 0, 5) != 'PHP 5') {
            throw new \RuntimeException($this->phpExecutable . ' is not a PHP executable');
        }
    }


    /**
     * Run the lint check using given PHP executable.
     * Returns empty string on success, or error message on failure.
     *
     * @param string $file Path to the file to lint
     * @return string
     * @throws RuntimeException File ... not found
     */
    public function check($file)
    {
        if (!file_exists($file)) {
            throw new \RuntimeException('File ' . $file . ' not found');
        }

        $cmd = $this->phpExecutable . ' -l ' . escapeshellarg($file) . ' 2>/dev/null';
        $output = trim(shell_exec($cmd));

        $cmp = 'No syntax errors';
        if (substr($output, 0, strlen($cmp)) == $cmp) {
            return '';
        }

        return $output;
    }
}
?>
