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

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

/**
 * Tests for the Token class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @covers spriebsch\PHPca\Token::getId
     */
    public function testGetId()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertEquals(T_OPEN_TAG, $t->getId());
    }

    /**
     * @covers spriebsch\PHPca\Token::setFile
     * @covers spriebsch\PHPca\Token::getFile
     */
    public function testSetAndGetFile()
    {
        $file = new File('filename', 'sourcecode');

        $t = new Token(T_OPEN_TAG, '<?php');
        $t->setFile($file);

        $this->assertEquals($file, $t->getFile());
    }

    /**
     * @covers spriebsch\PHPca\Token::getText
     */
    public function testGetText()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertEquals('<?php', $t->getText());
    }

    /**
     * @covers spriebsch\PHPca\Token::getLine
     */
    public function testGetLine()
    {
        $t = new Token(T_OPEN_TAG, '<?php', 5);
        $this->assertEquals(5, $t->getLine());
    }

    /**
     * @covers spriebsch\PHPca\Token::getLine
     */
    public function testGetLineReturnsNextLineWhenWhitespaceTokenHasLeadingNewline()
    {
        $t = new Token(T_WHITESPACE, "\n ", 3, 7);
        $this->assertEquals(4, $t->getLine());
    }

    /**
     * @covers spriebsch\PHPca\Token::getLine
     */
    public function testGetLineReturnsCorrectLineWhenWhitespaceTokenHasNoLeadingNewline()
    {
        $t = new Token(T_WHITESPACE, " ", 3, 7);
        $this->assertEquals(3, $t->getLine());
    }

    /**
     * @covers spriebsch\PHPca\Token::getColumn
     */
    public function testGetColumn()
    {
        $t = new Token(T_OPEN_TAG, '<?php', 0, 7);
        $this->assertEquals(7, $t->getColumn());
    }

    /**
     * @covers spriebsch\PHPca\Token::getColumn
     */
    public function testGetColumnReturnsOneWhenWhitespaceTokenHasLeadingNewline()
    {
        $t = new Token(T_WHITESPACE, "\n\n ", 3, 7);
        $this->assertEquals(1, $t->getColumn());
    }

    /**
     * @covers spriebsch\PHPca\Token::getColumn
     */
    public function testGetColumnReturnsRealValueWhenWhitespaceTokenHasNoLeadingNewLine()
    {
        $t = new Token(T_WHITESPACE, " ", 3, 7);
        $this->assertEquals(7, $t->getColumn());
    }

    /**
     * @covers spriebsch\PHPca\Token::getName
     */
    public function testGetName()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertEquals('T_OPEN_TAG', $t->getName());
    }

    /**
     * @covers spriebsch\PHPca\Token::getLength
     */
    public function testGetLength()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertEquals(5, $t->getLength());
    }

    /**
     * @covers spriebsch\PHPca\Token::getLength
     */
    public function testGetLengthForTokenWithTrailingWhitespace()
    {
        $t = new Token(T_OPEN_TAG, '<?php  ');
        $this->assertEquals(7, $t->getLength());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasNewLine
     */
    public function testHasNewLineReturnsFalseWhenTokenContainsNoNewLine()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertFalse($t->hasNewLine());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasNewLine
     */
    public function testHasNewLineReturnsTrueWhenTokenContainsNewLine()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n ");
        $this->assertTrue($t->hasNewLine());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasWhitespace
     */
    public function testHasWhitespaceReturnsTrueForBlank()
    {
        $t = new Token(T_OPEN_TAG, '<?php ');
        $this->assertTrue($t->hasWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasWhitespace
     */
    public function testHasWhitespaceReturnsTrueForCarriageReturn()
    {
        $t = new Token(T_OPEN_TAG, "<?php\r");
        $this->assertTrue($t->hasWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasWhitespace
     */
    public function testHasWhitespaceReturnsTrueForNewLine()
    {
        $t = new Token(T_OPEN_TAG, "<?php\n");
        $this->assertTrue($t->hasWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::hasWhitespace
     */
    public function testHasWhitespaceReturnsFalseForTokenWithoutWhitespace()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertFalse($t->hasWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::getNewLineCount
     */
    public function testNewLineCountIsZero()
    {
        $t = new Token(T_OPEN_TAG, "<?php ");
        $this->assertEquals(0, $t->getNewLineCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getNewLineCount
     */
    public function testNewLineCountIsOne()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n ");
        $this->assertEquals(1, $t->getNewLineCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getNewLineCount
     */
    public function testNewLineCountIsTwo()
    {
        $t = new Token(T_OPEN_TAG, "<?php\n\n  ");
        $this->assertEquals(2, $t->getNewLineCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getNewLineCount
     */
    public function testCRLFNewLineCountIsOne()
    {
        $t = new Token(T_OPEN_TAG, "<?php \r\n ");
        $this->assertEquals(1, $t->getNewLineCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getNewLineCount
     */
    public function testCRLFNewLineCountIsTwo()
    {
        $t = new Token(T_OPEN_TAG, "<?php \r\n\r\n ");
        $this->assertEquals(2, $t->getNewLineCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespace
     */
    public function testGetTrailingWhitespaceWithNewLine()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n  ");
        $this->assertEquals('  ', $t->getTrailingWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespace
     */
    public function testGetTrailingWhitespaceWithoutNewLine()
    {
        $t = new Token(T_OPEN_TAG, '<?php  ');
        $this->assertEquals('  ', $t->getTrailingWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespace
     */
    public function testGetTrailingWhitespaceIsEmptyWithNewLine()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n");
        $this->assertEquals('', $t->getTrailingWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespace
     */
    public function testGetTrailingWhitespaceIsEmptyWithoutNewLine()
    {
        $t = new Token(T_OPEN_TAG, '<?php');
        $this->assertEquals('', $t->getTrailingWhitespace());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespaceCount
     */
    public function testGetTrailingWhitespaceCountIsZero()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n");
        $this->assertEquals(0, $t->getTrailingWhitespaceCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespaceCount
     */
    public function testGetTrailingWhitespaceCountIsOne()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n ");
        $this->assertEquals(1, $t->getTrailingWhitespaceCount());
    }

    /**
     * @covers spriebsch\PHPca\Token::getTrailingWhitespaceCount
     */
    public function testGetTrailingWhitespaceCountIsTwo()
    {
        $t = new Token(T_OPEN_TAG, "<?php \n  ");
        $this->assertEquals(2, $t->getTrailingWhitespaceCount());
    }
}
?>