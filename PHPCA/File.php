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

/**
 * The File class represents a PHP source file as a sequence of Token objects
 * and provides several means of navigating these Tokens.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class File
{
  protected $fileName;
  protected $sourceCode;
  protected $tokens = array();
  protected $position = 0;


  /**
   * Constructs the File object
   *
   * We set the file name and source code to keep File independent
   * from the actual file system. Still, we'll need the file name
   * later when we output the result.
   *
   * @param string $fileName
   * @param string $sourceCode
   */
  public function __construct($fileName, $sourceCode)
  {
    $this->fileName   = $fileName;
    $this->sourceCode = $sourceCode;
  }


  public function getFileName()
  {
    return $this->fileName;
  }


  public function add(Token $token)
  {
    $this->tokens[] = $token;
  }


  public function isEndOfFile()
  {
    return $this->position >= count($this->tokens);
  }


  public function getToken()
  {
    return $this->tokens[$this->position];
  }


  /**
   * Returns an array with all tokens of given ID in the file.
   */
  public function getTokens($id)
  {
    if (!is_numeric($id)) {
      throw new \InvalidArgumentException('Numeric value required');
    }

    $result = array();

    foreach ($this->tokens as $token) {
      if ($token->getId() == $id) {
        $result[] = $token;
      }
    }

    return $result;
  }


  public function rewind()
  {
    $this->position = 0;
  }


  public function previous()
  {
    if ($this->position == 0) {
      throw new \OutOfBoundsException('Already at first token');
    }

    $this->position--;
  }


  public function next()
  {
    if ($this->isEndOfFile()) {
      throw new \OutOfBoundsException('Already at last token');
    }

    $this->position++;
  }


  public function gotoPosition($position)
  {
    if (!is_numeric($position)) {
      throw new \InvalidArgumentException('Numeric value required');
    }

    if ($position < 0 || $position > count($this->tokens) - 1) {
      throw new \OutOfBoundsException('Position ' . $position . ' does not exist');
    }

    $this->position = $position;
  }


  public function gotoToken(Token $token)
  {
    $this->position = $token->getPosition();
  }


  public function last()
  {
    $this->position = count($this->tokens) - 1;
  }


  /**
   * Returns all tokens as an array of token names.
   * Mainly useful for debugging purposes.
   */
  public function getTokenSequence()
  {
    $result = array();

    foreach ($this->tokens as $token) {
      $result[] = $token->getName();
    }

    return $result;
  }


  public function getPreviousToken()
  {
    if ($this->position == 0) {
      throw new \OutOfBoundsException('Already at first token');
    }

    return $this->tokens[$this->position - 1];
  }


  public function getNextToken()
  {
    if ($this->isEndOfFile()) {
      throw new \OutOfBoundsException('Already at last token');
    }

    return $this->tokens[$this->position + 1];
  }


  public function getNextTokens($number)
  {
    if ($this->isEndOfFile()) {
      throw new \OutOfBoundsException('Already at last token');
    }

    return array_slice($this->tokens, $this->position, $number);
  }


  public function skipTo($token)
  {
    for ($this->position; $this->position < count($this->tokens); $this->position++) {
      if ($this->tokens[$this->position]->getId() == $token) {
        return $this->getToken();
      }
    }

    throw new \RuntimeException('Token not found');
  }


  public function skipPast($token)
  {
    $this->skipTo($token);
    $this->next();

    return $this->getToken();
  }
}
?>
