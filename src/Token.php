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
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
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

use spriebsch\PHPca\Token as Token;

/**
 * The Token class wraps one PHP tokenizer token.
 * Each token knows its line and column in the source file,
 * and its numeric position in the token stream.
 * Where no PHP tokens exist (brackets, braces, etc.), 
 * we have defined our own in Constants.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class Token
{
  protected $id;
  protected $text;
  protected $line;
  protected $column;
  protected $position;
  protected $name;


  public function __construct($id, $text, $line, $column, $position)
  {
    $this->id       = $id;
    $this->text     = $text;
    $this->line     = $line;
    $this->column   = $column;
    $this->position = $position;

    $this->name     = Constants::getTokenName($id);
  }


  /**
   * Returns the numeric ID of the constant representing this token.
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }


  public function getText()
  {
    return $this->text;
  }


  public function getLine()
  {
    return $this->line;
  }


  public function getColumn()
  {
    return $this->column;
  }


  public function getPosition()
  {
    return $this->position;
  }


  /**
   * Returns the name of the constant representing this token as a string.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }


  /**
   * Returns the length in characters of this token.
   *
   * @return int
   */
  public function getLength()
  {
    return strlen($this->text);
  }


  public function hasNewLine()
  {
    return strstr($this->text, "\r") ||
           strstr($this->text, "\n");
  }


  public function getNewLineCount()
  {
    return substr_count($this->text, "\n");
  }


  public function hasWhitespace()
  {
    return strstr($this->text, "\r") !== false ||
           strstr($this->text, "\n") !== false ||
           strstr($this->text, "\t") !== false ||
           strstr($this->text, " ")  !== false;
  }


  public function getTrailingWhitespaceCount()
  {
    if (!$this->hasNewLine()) {
      return strlen($this->text);
    }

    return strlen(strrchr($this->text, "\n")) - 1;
  }
}

?>
