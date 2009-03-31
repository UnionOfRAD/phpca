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

namespace spriebsch\PHPca\Tests;

use spriebsch\PHPca\File as File;
use spriebsch\PHPca\Token as Token;


require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../src/bootstrap.php';


/**
 * Tests for the File class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @covers spriebsch\PHPca\File::add
   */
  public function testAdd()
  {
    $file = new File('filename', 'sourcecode');

    $token = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 1));
    $file->add($token);

    $this->assertAttributeContains($token, 'tokens', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::getToken
   */
  public function testGetToken()
  {
    $file = new File('filename', 'sourcecode');

    $token = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 1));
    $file->add($token);

    $this->assertEquals($token, $file->getToken());
  }


  /**
   * @covers spriebsch\PHPca\File::isEndOfFile
   */
  public function testIsEndOfFileReturnsFalse()
  {
    $file = new File('filename', 'sourcecode');

    $token = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 1));
    $file->add($token);

    $this->assertFalse($file->isEndOfFile());
  }


  /**
   * @covers spriebsch\PHPca\File::isEndOfFile
   */
  public function testIsEndOfFileReturnsTrue()
  {
    $file = new File('filename', 'sourcecode');
    $this->assertTrue($file->isEndOfFile());
  }


  /**
   * @covers spriebsch\PHPca\File::getTokens
   */
  public function testGetTokens()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);
    $token3 = new Token(T_OPEN_TAG, '<?php', 3, 1, 2);
    $token4 = new Token(T_WHITESPACE, ' ', 4, 1, 3);

    $file->add($token1);
    $file->add($token2);
    $file->add($token3);
    $file->add($token4);

    $this->assertEquals(array($token1, $token3), $file->getTokens(T_OPEN_TAG));
  }


  /**
   * @covers spriebsch\PHPca\File::getTokens
   * @expectedException \InvalidArgumentException
   */
  public function testGetTokensRequiresNumericArgument()
  {
    $file = new File('filename', 'sourcecode');
    $file->getTokens('nonsense');
  }


  /**
   * @covers spriebsch\PHPca\File::previous
   * @expectedException \RuntimeException
   */
  public function testPreviousThrowsRuntimeException()
  {
    $file = new File('filename', 'sourcecode');
    $file->previous();
  }


  /**
   * @covers spriebsch\PHPca\File::next
   */
  public function testNext()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));

    $file->add($token1);
    $file->add($token2);

    $file->next();

    $this->assertAttributeEquals(1, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::next
   * @expectedException \RuntimeException
   */
  public function testNextThrowsRuntimeException()
  {
    $file = new File('filename', 'sourcecode');
    $file->next();
  }


  /**
   * @covers spriebsch\PHPca\File::gotoPosition
   */
  public function testGotoPosition()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));

    $file->add($token1);
    $file->add($token2);

    $file->gotoPosition(1);

    $this->assertAttributeEquals(1, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::gotoPosition
   * @expectedException \InvalidArgumentException
   */
  public function testGotoPositionRequiresNumericArgument()
  {
    $file = new File('filename', 'sourcecode');
    $file->gotoPosition('nonsense');
  }


  /**
   * @covers spriebsch\PHPca\File::gotoPosition
   * @expectedException \OutOfBoundsException
   */
  public function testGotoPositionThrowsOutOfBoundsExceptionBeforeBeginning()
  {
    $file = new File('filename', 'sourcecode');
    $file->gotoPosition(-1);
  }


  /**
   * @covers spriebsch\PHPca\File::gotoPosition
   * @expectedException \OutOfBoundsException
   */
  public function testGotoPositionThrowsOutOfBoundExceptionPastEnd()
  {
    $file = new File('filename', 'sourcecode');
    $file->gotoPosition(1);
  }


  /**
   * @covers spriebsch\PHPca\File::gotoToken
   */
  public function testGotoToken()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);
    $token3 = new Token(T_OPEN_TAG, '<?php', 3, 1, 2);
    $token4 = new Token(T_WHITESPACE, ' ', 4, 1, 3);

    $file->add($token1);
    $file->add($token2);
    $file->add($token3);
    $file->add($token4);

    $file->gotoToken($token3);

    $this->assertAttributeEquals(2, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::last
   */
  public function testLast()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));

    $file->add($token1);
    $file->add($token2);

    $file->last();

    $this->assertAttributeEquals(1, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::rewind
   */
  public function testRewind()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));

    $file->add($token1);
    $file->add($token2);

    $file->last();
    $file->rewind();

    $this->assertAttributeEquals(0, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::previous
   */
  public function testPrevious()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));

    $file->add($token1);
    $file->add($token2);

    $file->last();
    $file->previous();

    $this->assertAttributeEquals(0, 'position', $file);
  }


  /**
   * @covers spriebsch\PHPca\File::getTokenSequence
   */
  public function testGetTokenSequence()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);

    $file->add($token1);
    $file->add($token2);

    $this->assertEquals(array('T_OPEN_TAG', 'T_WHITESPACE'), $file->getTokenSequence());
  }


  /**
   * @covers spriebsch\PHPca\File::getPreviousToken
   */
  public function testGetPreviousToken()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);

    $file->add($token1);
    $file->add($token2);

    $file->last();

    $this->assertEquals($token1, $file->getPreviousToken());

    // Make sure getPreviousToken() does not change the $position
    $this->assertEquals($token2, $file->getToken());
  }


  /**
   * @covers spriebsch\PHPca\File::getPreviousToken
   * @expectedException \OutOfBoundsException
   */
  public function testGetPreviousTokenThrowsOutOfBoundsException()
  {
    $file = new File('filename', 'sourcecode');
    $file->getPreviousToken();
  }


  /**
   * @covers spriebsch\PHPca\File::getNextToken
   */
  public function testGetNextToken()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);

    $file->add($token1);
    $file->add($token2);

    $this->assertEquals($token2, $file->getNextToken());

    // Make sure getNextToken() does not change the $position
    $this->assertEquals($token1, $file->getToken());
  }


  /**
   * @covers spriebsch\PHPca\File::getNextToken
   * @expectedException \OutOfBoundsException
   */
  public function testGetNextTokenThrowsOutOfBoundsException()
  {
    $file = new File('filename', 'sourcecode');
    $file->getNextToken();
  }


  /**
   * @covers spriebsch\PHPca\File::getNextTokens
   */
  public function testGetNextTokens()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = $this->getMock('spriebsch\PHPca\Token', array(), array(0, 't1', 1, 1, 0));
    $token2 = $this->getMock('spriebsch\PHPca\Token', array(), array(1, 't2', 1, 1, 1));
    $token3 = $this->getMock('spriebsch\PHPca\Token', array(), array(2, 't3', 1, 1, 2));

    $file->add($token1);
    $file->add($token2);
    $file->add($token3);

    $file->next();

    $this->assertEquals(array($token2, $token3), $file->getNextTokens(2));
  }


  /**
   * @covers spriebsch\PHPca\File::getNextTokens
   * @expectedException \OutOfBoundsException
   */
  public function testGetNextTokensThrowsOutOfBoundsException()
  {
    $file = new File('filename', 'sourcecode');
    $file->getNextTokens(1);
  }


  /**
   * @covers spriebsch\PHPca\File::skipTo
   */
  public function testSkipTo()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);

    $file->add($token1);
    $file->add($token2);

    $file->skipTo(T_WHITESPACE);

    $this->assertEquals($token2, $file->getToken());
  }


  /**
   * @covers spriebsch\PHPca\File::skipTo
   * @expectedException \RuntimeException
   */
  public function testSkipToThrowsRuntimeException()
  {
    $file = new File('filename', 'sourcecode');

    $token = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $file->add($token);

    $file->skipTo(9999);
  }


  /**
   * @covers spriebsch\PHPca\File::skipPast
   */
  public function testSkipPast()
  {
    $file = new File('filename', 'sourcecode');

    $token1 = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $token2 = new Token(T_WHITESPACE, ' ', 2, 1, 1);
    $token3 = new Token(T_OPEN_TAG, '<?php', 3, 1, 0);

    $file->add($token1);
    $file->add($token2);
    $file->add($token3);

    $file->skipPast(T_WHITESPACE);

    $this->assertEquals($token3, $file->getToken());
  }


  /**
   * @covers spriebsch\PHPca\File::skipPast
   * @expectedException \RuntimeException
   */
  public function testSkipPastThrowsRuntimeException()
  {
    $file = new File('filename', 'sourcecode');

    $token = new Token(T_OPEN_TAG, '<?php', 1, 1, 0);
    $file->add($token);

    $file->skipPast(9999);
  }
}

?>
