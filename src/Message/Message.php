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
 * Generic result message base class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Message
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var int
     */
    protected $column;

    /**
    * Creates the Message object.
    *
    * @param string $fileName
    * @param string $message
    * @param Token $token
    * @param int $line Line number that message refers to (for multiline tokens)
    * @param int $column
    */
    public function __construct($fileName, $message, Token $token = null, $line = null, $column = null)
    {
        $this->fileName = $fileName;
        $this->message  = $message;
        $this->token    = $token;
        $this->line     = $line;
        $this->column   = $column;
    }

    /**
    * Returns the file name.
    *
    * @returns string
    */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
    * Returns the message text
    *
    * @returns string
    */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the line in the source file the message refers to.
     * If this error message does not refer to a token (lint error),
     * the line number is 0.
     *
     * @returns integer
     */
    public function getLine()
    {
        // Explicitly set line overrides token start line
        if ($this->line !== null) {
            return $this->line;
        }

        if (!$this->token instanceOf Token) {
            return 0;
        }
        
        return $this->token->getLine();
    }

    /**
     * Returns the column in the source file the message refers to.
     * If this error message does not refer to a token (lint error),
     * the column number is 0.
     *
     * @returns integer
     */
    public function getColumn()
    {
        // Explicitly set column overrides token start column
        if ($this->column !== null) {
            return $this->column;
        }

        if (!$this->token instanceOf Token) {
            return 0;
        }
    
        return $this->token->getColumn();
    }

    /**
     * Returns the token the message refers to.
     *
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
?>