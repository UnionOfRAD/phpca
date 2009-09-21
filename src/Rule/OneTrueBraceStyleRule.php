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

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Finder;
use spriebsch\PHPca\Error;
use spriebsch\PHPca\Warning;

use spriebsch\PHPca\Pattern\Pattern;
use spriebsch\PHPca\Pattern\Token;

/**
 * No tabulator rule. Makes sure that only blanks are used for indentation.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class OneTrueBraceStyleRule extends Rule
{
    /**
     * Helper function to generate violation depending on
     * whether token and brace are on same or different line.
     *
     * @param string $firstToken Token ID
     * @param string $error Error message
     * @param bool $sameLine
     */
    protected function checkBraceLine($firstToken, $error, $sameLine = true)
    {
        // Pattern from first token until the next open curly brace
        $pattern = new Pattern();
        $pattern->token($firstToken)
                ->zeroOrMore(new Token(T_ANY))
                ->token(T_OPEN_CURLY);

        foreach (Finder::findPattern($this->file, $pattern) as $match) {

            $token = $match[0];
            $brace = $match[sizeof($match) - 1];

            $line = $token->getLine();
            $braceLine = $brace->getLine();

            // Generate error if token and brace are on same line
            if ($sameLine && $line == $braceLine) {
                $this->addViolation($error, $match[0]);
            }

            // Generate error if token and brace are on different lines
            if (!$sameLine && $line != $braceLine) {
                $this->addViolation($error, $match[0]);
            }
        }
    }

    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        $this->checkBraceLine(T_CLASS, 'class: curly brace on same line');
        $this->checkBraceLine(T_FUNCTION, 'function: curly brace on same line');

        $this->checkBraceLine(T_FOREACH, 'foreach: curly brace on different line', false);
        $this->checkBraceLine(T_FOR, 'for: curly brace on different line', false);
        $this->checkBraceLine(T_SWITCH, 'switch: curly brace on different line', false);
        $this->checkBraceLine(T_IF, 'if: curly brace on different line', false);
        $this->checkBraceLine(T_ELSE, 'else: curly brace on different line', false);
        $this->checkBraceLine(T_ELSEIF, 'elseif: curly brace on different line', false);
        $this->checkBraceLine(T_WHILE, 'while: curly brace on different line', false);
        $this->checkBraceLine(T_DO, 'do: curly brace on different line', false);

        // Namespaces have a dual syntax, either "namespace name;" or
        // "namespace name {}", so we need a different search pattern
        // to avoid wrong matches like "namespace ... class {".
        $pattern = new Pattern();
        $pattern->token(T_NAMESPACE)
                ->token(T_WHITESPACE)
                ->token(T_STRING)
                ->token(T_WHITESPACE)
                ->token(T_OPEN_CURLY);

        foreach (Finder::findPattern($this->file, $pattern) as $match)
        {
            $token = $match[0];
            $brace = $match[sizeof($match) - 1];

            $line = $token->getLine();
            $braceLine = $brace->getLine();

            // Generate error if namespace token and brace are on same line
            if ($line == $braceLine) {
                $this->addViolation('namespace: curly brace on same line', $token);
            }
        }
    }
}
?>