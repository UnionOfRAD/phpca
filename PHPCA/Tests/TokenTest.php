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
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 * @license    BSD License
 */

namespace spriebsch\PHPca\Tests;

use spriebsch\PHPca\Token as Token;

require_once 'PHPUnit/Framework.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Tests for the Token class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers Token::getId
    */
    public function testGetIdReturnsTokenId()
    {
        $t = new Token(367, '<?php', 1, 0, 0);
        $this->assertEquals(367, $t->getId());
    }

    /**
    * @covers Token::getName
    */
    public function testGetNameReturnsTokenName()
    {
        $t = new Token(367, '<?php', 1, 0, 0);
        $this->assertEquals('T_DOC_COMMENT', $t->getName());
    }

    /**
    * @covers Token::getText
    */
    public function testGetTextReturnsTokenText()
    {
        $t = new Token(367, '<?php', 0, 0, 0);
        $this->assertEquals('<?php', $t->getText());
    }

    /**
    * @covers Token::getLine
    */
    public function testGetLineReturnsTokenLine()
    {
        $t = new Token(367, '<?php', 1, 0, 0);
        $this->assertEquals(1, $t->getLine());
    }

    /**
    * @covers Token::getColumn
    */
    public function testGetColumnReturnsTokenColumn()
    {
        $t = new Token(367, '<?php', 0, 1, 0);
        $this->assertEquals(1, $t->getColumn());
    }

    /**
    * @covers Token::getPosition
    */
    public function testGetPositionReturnsTokenPosition()
    {
        $t = new Token(367, '<?php', 0, 0, 1);
        $this->assertEquals(1, $t->getPosition());
    }

    /**
    * @covers Token::getLength
    */
    public function testGetLengthReturnsTokenLength()
    {
        $t = new Token(367, '<?php', 0, 0, 0);
        $this->assertEquals(5, $t->getLength());
    }

    /**
    * @covers Token::hasNewLine
    */
    public function testHasNewLineReturnsTrue()
    {
        $t = new Token(367, "<?php \n ", 0, 0, 0);
        $this->assertTrue($t->hasNewLine());
    }

    /**
    * @covers Token::hasNewLine
    */
    public function testHasNewLineReturnsFalse()
    {
        $t = new Token(367, "<?php ", 0, 0, 0);
        $this->assertFalse($t->hasNewLine());
    }

    /**
    * @covers Token::hasWhitespace
    */
    public function testHasWhitespaceReturnsTrueForBlank()
    {
        $t = new Token(367, '<?php ', 0, 0, 0);
        $this->assertTrue($t->hasWhitespace());
    }

    /**
    * @covers Token::hasWhitespace
    */
    public function testHasWhitespaceReturnsTrueForCarriageReturn()
    {
        $t = new Token(367, "<?php\r", 0, 0, 0);
        $this->assertTrue($t->hasWhitespace());
    }

    /**
    * @covers Token::hasWhitespace
    */
    public function testHasWhitespaceReturnsTrueForNewLine()
    {
        $t = new Token(367, "<?php\n", 0, 0, 0);
        $this->assertTrue($t->hasWhitespace());
    }

    /**
    * @covers Token::hasWhitespace
    */
    public function testHasWhitespaceReturnsFalse()
    {
        $t = new Token(367, '<?php', 0, 0, 0);
        $this->assertFalse($t->hasWhitespace());
    }

    /**
    * @covers Token::getNewLineCount
    */
    public function testNewLineCountIsZero()
    {
        $t = new Token(367, "<?php ", 0, 0, 0);
        $this->assertEquals(0, $t->getNewLineCount());
    }

    /**
    * @covers Token::getNewLineCount
    */
    public function testNewLineCountIsOne()
    {
        $t = new Token(367, "<?php \n ", 0, 0, 0);
        $this->assertEquals(1, $t->getNewLineCount());
    }

    /**
    * @covers Token::getNewLineCount
    */
    public function testNewLineCountIsTwo()
    {
        $t = new Token(367, "<?php\n\n  ", 0, 0, 0);
        $this->assertEquals(2, $t->getNewLineCount());
    }

    /**
    * @covers Token::getNewLineCount
    */
    public function testCRLFNewLineCountIsOne()
    {
        $t = new Token(367, "<?php \r\n ", 0, 0, 0);
        $this->assertEquals(1, $t->getNewLineCount());
    }

    /**
    * @covers Token::getNewLineCount
    */
    public function testCRLFNewLineCountIsTwo()
    {
        $t = new Token(367, "<?php \r\n\r\n ", 0, 0, 0);
        $this->assertEquals(2, $t->getNewLineCount());
    }

    /**
    * @covers Token::getTrailingWhitespaceCount
    */
    public function testGetTrailingWhitespaceCountForEmptyString()
    {
        $t = new Token(0, '', 0, 0, 0);
        $this->assertEquals(0, $t->getTrailingWhitespaceCount());
    }

    /**
    * @covers Token::getTrailingWhitespaceCount
    */
    public function testGetTrailingWhitespaceCountForStringWithoutNewline()
    {
        $t = new Token(367, '<?php', 0, 0, 0);
        $this->assertEquals(0, $t->getTrailingWhitespaceCount());
    }

    /**
    * @covers Token::getTrailingWhitespaceCount
    */
    public function testGetTrailingWhitespaceCountIsZero()
    {
        $t = new Token(367, "<?php \n", 0, 0, 0);
        $this->assertEquals(0, $t->getTrailingWhitespaceCount());
    }

    /**
    * @covers Token::getTrailingWhitespaceCount
    */
    public function testGetTrailingWhitespaceCountIsOne()
    {
        $t = new Token(367, "<?php \n ", 0, 0, 0);
        $this->assertEquals(1, $t->getTrailingWhitespaceCount());
    }

    /**
    * @covers Token::getTrailingWhitespaceCount
    */
    public function testGetTrailingWhitespaceCountIsTwo()
    {
        $t = new Token(367, "<?php \n  ", 0, 0, 0);
        $this->assertEquals(2, $t->getTrailingWhitespaceCount());
    }

    /**
    * @covers Token::toHex
    */
    public function testToHex()
    {
        $t = new Token(367, "<?php", 0, 0, 0);
        $this->assertEquals('61 62 63', $t->toHex('abc'));
    }

    /**
    * @covers Token::toHex
    */
    public function testToHexForOneDigitCharacter()
    {
        $t = new Token(367, "<?php", 0, 0, 0);
        $this->assertEquals('0a', $t->toHex("\n"));
    }
}
?>
