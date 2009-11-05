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

/**
 * Makes sure that the file indented correctly.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class IndentationRule extends Rule
{
    protected $indentation = '    ';

    /**
     * Checks indentation of a docblock.
     *
     * @param spriebsch\PHPca\Token The docblock zoken
     * @return null
     */
    protected function checkDocBlock(\spriebsch\PHPca\Token $token)
    {
        // normalize line endings, create array of docblock lines
        $lines = explode("\n", str_replace("\r", '', $token->getText()));

        // first docblock line has been taken care of by the
        // general indentation check below

        // all other lines of the docblock must start with one space
        for ($i = 1; $i < sizeof($lines); $i++) {
            if (strlen(ltrim($lines[$i])) != strlen($lines[$i]) - 1) {
                $this->addViolation('wrong indentation in line ' . $i . ' of docblock', $token);
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
        while ($this->file->seekTokenId(T_DOC_COMMENT)) {
            $this->checkDocBlock($this->file->current());
            $this->file->next();
        }

        $this->file->rewind();

        while (true) {

            $token = $this->file->current();

            $blockLevel = $token->getBlockLevel();

            // whitespace before a closing brace counts as if
            // in the outer block level
            $this->file->next();
            if ($this->file->current()->getId() == T_CLOSE_CURLY) {
                $blockLevel--;
            }
            $this->file->prev();

            $indentString = str_repeat($this->indentation, $blockLevel);
            $fileIndentation = $token->getTrailingWhitespace();

            if ($fileIndentation != $indentString) {
                 $this->addViolation('wrong indentation', $this->file->current());
            }

            if (!$this->file->seekNextLine()) {
                return;
            }
        }
    }
}
?>