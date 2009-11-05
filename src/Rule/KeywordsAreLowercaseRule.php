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
 * Make sure that all keywords are lowercase.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class KeywordsAreLowercaseRule extends Rule
{
    protected $keywords = array(
        T_CLASS,
        T_FUNCTION,
        T_PRIVATE,
        T_PROTECTED,
        T_PUBLIC,
        T_INTERFACE,
        T_ABSTRACT,
        T_ARRAY,
        T_ARRAY_CAST,
        T_BREAK,
        T_CATCH,
        T_CASE,
        T_CLONE,
        T_CONST,
        T_CONTINUE,
        T_DECLARE,
        T_DEFAULT,
        T_DO,
        T_DOUBLE_CAST,
        T_ECHO,
        T_ELSE,
        T_ELSEIF,
        T_EMPTY,
        T_ENDDECLARE,
        T_ENDFOR,
        T_ENDFOREACH,
        T_ENDIF,
        T_ENDSWITCH,
        T_ENDWHILE,
        T_END_HEREDOC,
        T_EVAL,
        T_EXIT,
        T_EXTENDS,
        T_FINAL,
        T_FOR,
        T_FOREACH,
        T_GLOBAL,
        T_GOTO,
        T_HALT_COMPILER,
        T_IF,
        T_IMPLEMENTS,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_INSTANCEOF,
        T_INT_CAST,
        T_ISSET,
        T_LIST,
        T_LOGICAL_AND,
        T_LOGICAL_OR,
        T_LOGICAL_XOR,
        T_NEW,
        T_PRINT,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_RETURN,
        T_STATIC,
        T_STRING_CAST,
        T_SWITCH,
        T_THROW,
        T_TRY,
        T_UNSET,
        T_UNSET_CAST,
        T_USE,
        T_VAR,
        T_WHILE,
    );

    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        foreach ($this->keywords as $keyword) {
            $this->file->rewind();

            while ($this->file->seekTokenId($keyword)) {
                $token = $this->file->current();

                $text = $token->getText();
                if ($text != strtolower($text)) {
                    $this->addViolation('keyword ' . strtolower(trim($text)) . ' not lowercase', $token);
                }

                $this->file->next();
            }
        }
    }
}
?>