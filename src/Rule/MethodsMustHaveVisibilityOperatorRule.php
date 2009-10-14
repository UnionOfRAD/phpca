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

use spriebsch\PHPca\Pattern\Pattern;
use spriebsch\PHPca\Pattern\Token;

/**
 * Makes sure that every method has a visibility operator.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class MethodsMustHaveVisibilityOperatorRule extends Rule
{
    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        $this->file->rewind();

        while (true) {
            try {
                $this->file->seekTokenId(T_FUNCTION);
                $functionToken = $this->file->current();

                if ($functionToken->getClass() == '' && $functionToken->getInterface() == '') {
                    $this->file->next();
                    continue;
                }
            }

            catch (\spriebsch\PHPca\Exception $e) {
                // No more T_FUNCTION tokens found, we are done.
                return;
            }

            try {

                // Skip T_WHITESPACE
                $this->file->prev();

                $this->file->prev();

                if ($this->file->current()->getName() == 'T_STATIC') {

                    // Skip T_STATIC and T_WHITESPACE
                    $this->file->prev();
                    $this->file->prev();
                }

                if (!in_array($this->file->current()->getName(), array('T_PUBLIC', 'T_PROTECTED', 'T_PRIVATE'))) {
                    $this->addViolation('Function has no visibility operator', $functionToken);
                }

                // Search backwards for next visibility operator.
//                $this->file->seekTokenId(T_DOC_COMMENT, true);

                // Docblock must end exactly one line above function token,
                // otherwise it can be the docblock of another function.
//                if (($this->file->current()->getEndLine() + 1) != $functionToken->getLine()) {
//                    $this->addViolation('Function has no docblock comment', $functionToken);
//                }
            }

            catch (\spriebsch\PHPca\Exception $e) {
                // Search for the docblock has failed,
                $this->addViolation('Function has no docblock comment', $functionToken);
            }

            // Seek back to the token following the T_FUNCTION we just processed.
            $this->file->seekToken($functionToken);
            $this->file->next();
        }
    }
}
?>
