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

class Indentation extends Rule
{
    protected $indentText = '    ';

    protected function getIndentText($indentLevel)
    {
        return str_repeat($this->indentText, $indentLevel);
    }

    protected function doCheck()
    {
        $line = 1;
        $indentLevel = 0;

        while (!$this->file->isEndOfFile()) {
            $curr = $this->file->getToken();

            // Closing curly brace ends a block
            if ($curr->getId() == T_CLOSE_CURLY) {
                $indentLevel--;
            }

            if ($curr->getLine() > $line) {
                $line = $curr->getLine() + 1;
     
                $prev = $this->file->getPreviousToken();

                // Ignore blank linkes as they are obviously not indented
                if ($curr->hasNewline() && $curr->getTrailingWhitespaceCount() == 0) {
                    continue;
                }

/*
var_dump($curr->getName());
var_dump('line ' . $line . ' col ' . $curr->getColumn());
*/

                if ($curr->getColumn() != 1 + strlen($this->getIndentText($indentLevel))) {
                    $this->addMessage(Message::ERROR, 'Wrong indentation', $curr);
                }
            }

            // Opening curly brace starts a block
            if ($curr->getId() == T_OPEN_CURLY) {
                $indentLevel++;
            }

            $this->file->next(); 
        }
    }
}
?>