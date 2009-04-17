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

class OneTrueBraceStyle extends Rule
{
  protected function doCheck()
  {
    // class requires curly braces on next line
    foreach ($this->file->getTokens(array(T_CLASS)) as $token) {
      $this->file->gotoToken($token);

      $token_line = $this->file->getToken()->getLine();
      $brace_line = $this->file->skipTo(T_OPEN_CURLY)->getLine();

      if ($token_line == $brace_line) {
        $this->addMessage(Message::ERROR, 'Opening curly brace for class must be on next line', $token);
      }
    }


    // function requires curly braces on next line
    foreach ($this->file->getTokens(array(T_FUNCTION)) as $token) {
      $this->file->gotoToken($token);

      $token_line = $this->file->getToken()->getLine();
      $brace_line = $this->file->skipTo(T_OPEN_CURLY)->getLine();

      if ($token_line == $brace_line) {
        $this->addMessage(Message::ERROR, 'Opening curly brace for function must be on next line', $token);
      }
    }


    // if requires curly braces on the next line
    foreach ($this->file->getTokens(array(T_IF)) as $token) {
      $this->file->gotoToken($token);

      $token_line = $this->file->getToken()->getLine();
      $brace_line = $this->file->skipTo(T_OPEN_CURLY)->getLine();

      if ($token_line != $brace_line) {
        $this->addMessage(Message::ERROR, 'Opening curly brace for if must be on same line', $token);
      }
    }


    // foreach requires curly braces on the same line
    foreach ($this->file->getTokens(array(T_FOREACH)) as $token) {
      $this->file->gotoToken($token);

      $token_line = $this->file->getToken()->getLine();
      $brace_line = $this->file->skipTo(T_OPEN_CURLY)->getLine();

      if ($token_line != $brace_line) {
        $this->addMessage(Message::ERROR, 'Opening curly brace of foreach must be on same line', $token);
      }
    }
  }
}

?>
