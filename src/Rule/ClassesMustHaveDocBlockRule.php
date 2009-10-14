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
 * Make sure that every class has a docblock.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ClassesMustHaveDocBlockRule extends Rule
{
    /**
     * Makes sure that each class has a docblock.
     *
     * @returns null
     */
    protected function doCheck()
    {
        // We are interested in T_CLASS tokens.
        while ($this->file->trySeekTokenId(T_CLASS)) {

            // Remember the class token, we need seek back to here later.
            $classToken = $this->file->current();

            // Search backwards for next docblock.
            if ($this->file->trySeekTokenId(T_DOC_COMMENT, true)) {

                // Docblock must end exactly one line above class token,
                // otherwise it can be the something else's docblock.
                if (($this->file->current()->getEndLine() + 1) != $classToken->getLine()) {
                    $this->addViolation('Class has no docblock comment', $classToken);
                }
            } else {

                // No docblock found, that's certainly wrong.
                $this->addViolation('Class has no docblock comment', $classToken);
            }

            // Seek back to the token following the T_CLASS we just processed.
            $this->file->seekToken($classToken);
            $this->file->next();
        }
    }
}
?>