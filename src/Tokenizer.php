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
 * Creates a File object holding a collection of Tokens
 * from given source code. Uses PHP's built-in tokenizer but
 * adds on some custom tokens
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Tokenizer
{
    /**
     * Tokenize a file
     *
     * @param string $fileName    the file name 
     * @param string $sourceCode  the source code
     * @return File
     */
    static public function tokenize($fileName, $sourceCode)
    {
        Constants::init();

        $classFound = false;
        $waitForClassBegin = false;
        $classCurlyLevel = 0;

        $functionFound = false;
        $waitForFunctionBegin = false;
        $functionCurlyLevel = 0;

        $namespace = '\\';
        $class = '';
        $function = '';

        $level = 0;

        $line = 1;
        $column = 1;

        $file = new File($fileName, $sourceCode);

        foreach(token_get_all($sourceCode) as $token) {
            if (is_array($token)) {
                $id   = $token[0];
                $text = $token[1];
                $line = $token[2];
           } else {
            
                try {
                    // it's not a PHP token, so we use one we have defined
                    $id   = Constants::getTokenId($token);
                    $text = $token;
                }

                // @codeCoverageIgnoreStart
                catch (UnkownTokenException $e) {
                    throw new TokenizerException('Unknown token ' . $e->getTokenName() . ' in file ' . $fileName);
                }
                // This exception is not testable, because we _have_ defined all
                // tokens, hopefully. It's just a safeguard to provide a decent
                // error message should we ever encounter an undefined token.
                // @codeCoverageIgnoreEnd
            }

            $tokenObj = new Token($id, $text, $line, $column);

            if ($tokenObj->hasNewline()) {
                // a newline resets the column count
                $line  += $tokenObj->getNewLineCount();
                $column = 1 + $tokenObj->getTrailingWhitespaceCount();
            } else {
                $column += $tokenObj->getLength();
            }

            // We have encountered a T_CLASS token before (this is indicated
            // by $classFound being true, so the T_STRING contains the class
            // name (there will be T_WHITESPACE between T_CLASS and T_STRING).
            // We remember the class name, but do not set it until we have
            // encountered the next opening brace. We set $waitForClassBegin
            // to true so that we can wait for the next opening curly brace.
            if ($classFound && $tokenObj->getId() == T_STRING) {
                $class = $tokenObj->getText();
                $waitForClassBegin = true;
                $classFound = false;
            }

            // We have encountered a T_FUNCTION token before (this is indicated
            // by $functionFound being true, so the T_STRING contains the class
            // name (there will be T_WHITESPACE between T_FUNCTION and T_STRING).
            // We remember the function name, but do not set it until we have
            // encountered the next opening brace. We set $waitForFunctionBegin
            // to true so that we can wait for the next opening curly brace.
            if ($functionFound && $tokenObj->getId() == T_STRING) {
                $function = $tokenObj->getText();
                $waitForFunctionBegin = true;
                $functionFound = false;
            }

            // If we encounter a T_CLASS token, we have found a class definition.
            // We set $classFound to true so that we can watch out for the class
            // name (see above).
            if ($tokenObj->getId() == T_CLASS) {
                $classFound = true;
            }

            // If we encounter a T_FUNCTION token, we have found a function.
            // We set $functionFound to true so that we can watch out for the
            // function name (see above).
            if ($tokenObj->getId() == T_FUNCTION) {
                $functionFound = true;
            }

            // Opening curly brace opens another block, thus increases the level.
            if ($tokenObj->getId() == T_OPEN_CURLY) {
                $level++;

                // If we encounter the opening curly brace of a class (this happens
                // when $waitForClassBegin is true), we remember the block level of
                // this brace so that we can end the class when we encounter the
                // matching closing tag.
                if ($waitForClassBegin) {
                    $classCurlyLevel = $level;
                    $waitForClassBegin = false;
                }

                // If we encounter the opening curly brace of a class (this happens
                // when $waitForClassBegin is true), we remember the block level of
                // this brace so that we can end the class when we encounter the
                // matching closing tag.
                if ($waitForFunctionBegin) {
                    $functionCurlyLevel = $level;
                    $waitForFunctionBegin = false;
                }
            }

            // This also sets the class when we are outside the class,
            // which is harmless because we then just set an emtpy string.
            if (!$waitForClassBegin) {
                $tokenObj->setClass($class);
// @todo namespace the name!
            }

            // This also sets the function when we are outside the function,
            // which is harmless because we then just set an emtpy string.
            if (!$waitForFunctionBegin) {
                $tokenObj->setFunction($function);
            }

            $tokenObj->setBlockLevel($level);

            // Closing curly decreases the block level. We do this *after*
            // we have set the block leven in the current token, so that
            // the closing curly's level matches the level of its opening brace.
            if ($tokenObj->getId() == T_CLOSE_CURLY) {
                $level--;

                // If we are inside a class and the closing brace matches the
                // opening brace of that class, the block/class has ended.
                if ($class != '' && $tokenObj->getBlockLevel() == $classCurlyLevel) {
                    $class = '';
                    $classCurlyLevel = 0;
                }

                // If we are inside a function and the closing brace matches the
                // opening brace of that function, the block/function has ended.
                if ($function != '' && $tokenObj->getBlockLevel() == $functionCurlyLevel) {
                    $function = '';
                    $functionCurlyLevel = 0;
                }
            }

            $file->add($tokenObj);
        }

        return $file;
    }
}
?>