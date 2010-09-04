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
 * Tests for the File class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Dummy test to achieve constructor code coverage.
     *
     * @covers spriebsch\PHPca\File::__construct
     */
    public function testConstruct()
    {
        $file = new File('filename', 'sourcecode');
    }
	
    /**
     * @covers spriebsch\PHPca\File::getFilename
     */
    public function testGetFileName()
    {
        $file = new File('filename', 'sourcecode');
        $this->assertEquals('filename', $file->getFileName());
    }

    /**
     * @covers spriebsch\PHPca\File::getSourceCode
     */
    public function testGetSourceCode()
    {
        $file = new File('filename', 'sourcecode');
        $this->assertEquals('sourcecode', $file->getSourceCode());
    }

    /**
     * @covers spriebsch\PHPca\File::getNamespaces
     */
    public function testGetNamespaces()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/namespaces.php'));
    	$this->assertEquals(array('A\\B', 'B\\C', 'D\\E\\F'), $file->getNamespaces());
    }    	

    /**
     * @covers spriebsch\PHPca\File::getNamespaces
     */
    public function testGetNamespacesWithCurlyBraces()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/namespaces_curly.php'));
        $this->assertEquals(array('A\\B', 'B\\C', 'D\\E\\F'), $file->getNamespaces());
    }       

    /**
     * @covers spriebsch\PHPca\File::getClasses
     */
    public function testGetClasses()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/classes.php'));
        $this->assertEquals(array('A', 'B', 'C'), $file->getClasses());
    }       

    /**
     * @covers spriebsch\PHPca\File::getFunctions
     */
    public function testGetFunctions()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/functions.php'));

        $this->assertEquals(array('a', 'b', 'c'), $file->getFunctions());
    }       

    /**
     * @covers spriebsch\PHPca\File::getMethods
     */
    public function testGetMethods()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/functions.php'));

        $this->assertEquals(array('x', 'y', 'z', 'a', 'b', 'c'), $file->getMethods('A'));
    }       

    /**
     * @covers spriebsch\PHPca\File::getMethods
     */
    public function testGetMethodsFindsAbstractMethods()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/functions.php'));

        $this->assertEquals(array('a', 'b'), $file->getMethods('B'));
    }       

    /**
     * @covers spriebsch\PHPca\File::__toString
     * @covers spriebsch\PHPca\File::add
     */
    public function testToString()
    {
        $file = new File('filename', 'sourcecode');

        $token = new Token(T_OPEN_TAG, '<?php', 1, 1);
        $file->add($token);

        $token = new Token(T_CLOSE_TAG, '?>', 1, 1);
        $file->add($token);

        $this->assertEquals('T_OPEN_TAG T_CLOSE_TAG', (string) $file);
    }

    /**
     * @covers spriebsch\PHPca\File::seek
     * @covers spriebsch\PHPca\File::add
     */
    public function testSeek()
    {
        $file = new File('filename', 'sourcecode');
        $file->add(new Token(T_OPEN_TAG, '<?php'));
        $file->add(new Token(T_CLASS, 'class'));
        $file->add(new Token(T_FUNCTION, 'function'));
        $file->add(new Token(T_CLOSE_TAG, '?>'));

        $file->seek(2);

        $this->assertEquals('T_FUNCTION', $file->current()->getName());
    }

    /**
     * @covers spriebsch\PHPca\File::seekToken
     * @covers spriebsch\PHPca\File::add
     */
    public function testSeekToken()
    {
        $token = new Token(T_CLASS, 'class');

        $file = new File('filename', 'sourcecode');
        $file->add(new Token(T_OPEN_TAG, '<?php'));
        $file->add(new Token(T_FUNCTION, 'function'));
        $file->add($token);
        $file->add(new Token(T_CLOSE_TAG, '?>'));

        $file->seekToken($token);

        $this->assertSame($token, $file->current());
    }

    /**
     * @covers spriebsch\PHPca\File::seekTokenId
     * @covers spriebsch\PHPca\File::add
     */
    public function testSeekTokenId()
    {
        $file = new File('filename', 'sourcecode');
        $file->add(new Token(T_OPEN_TAG, '<?php'));
        $file->add(new Token(T_FUNCTION, 'function'));
        $file->add(new Token(T_CLASS, 'class'));
        $file->add(new Token(T_CLOSE_TAG, '?>'));

        $file->rewind();
        $file->seekTokenId(T_CLASS);

        $this->assertEquals('T_CLASS', $file->current()->getName());
    }

    /**
     * @covers spriebsch\PHPca\File::seekMatchingCurlyBrace
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testSeekMatchingCurlyBraceThrowsExceptionOnNonCurlyBrace()
    {
        $file = new File('filename', 'sourcecode');

        $file->seekMatchingCurlyBrace(new Token(T_OPEN_TAG, '<?php'));
    }

    /**
     * @covers spriebsch\PHPca\File::seekMatchingCurlyBrace
     */
    public function testSeekMatchingCurlyBraceForFunction()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/braces.php'));
        $file->rewind();

        $file->seekTokenId(T_FUNCTION);
        $file->seekTokenId(T_OPEN_CURLY);
        $openBrace = $file->current();

        $file->seekMatchingCurlyBrace($openBrace);

        $this->assertEquals('T_CLOSE_CURLY', $file->current()->getName());
        $this->assertEquals(24, $file->current()->getLine());
        $this->assertEquals($openBrace->getBlockLevel(), $file->current()->getBlockLevel());
    }

    /**
     * @covers spriebsch\PHPca\File::seekMatchingCurlyBrace
     */
    public function testSeekMatchingCurlyBraceForFunctionReverse()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/braces.php'));

        $file->seek(sizeof($file) - 1);
        $file->prev();
        $file->prev();
        $file->prev();
        $file->prev();
        $closeBrace = $file->current();

        $file->seekMatchingCurlyBrace($closeBrace);

        $this->assertEquals('T_OPEN_CURLY', $file->current()->getName());
        $this->assertEquals(9, $file->current()->getLine());
        $this->assertEquals($closeBrace->getBlockLevel(), $file->current()->getBlockLevel());
    }

    /**
     * @covers spriebsch\PHPca\File::seekMatchingCurlyBrace
     */
    public function testSeekMatchingCurlyBraceForClass()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/braces.php'));
        $file->rewind();

        $file->seekTokenId(T_CLASS);
        $file->seekTokenId(T_OPEN_CURLY);
        $openBrace = $file->current();

        $file->seekMatchingCurlyBrace($openBrace);

        $this->assertEquals('T_CLOSE_CURLY', $file->current()->getName());
        $this->assertEquals(25, $file->current()->getLine());
        $this->assertEquals($openBrace->getBlockLevel(), $file->current()->getBlockLevel());
    }

    /**
     * @covers spriebsch\PHPca\File::seekMatchingCurlyBrace
     */
    public function testSeekMatchingCurlyBraceForClassReverse()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/braces.php'));

        $file->seek(sizeof($file) - 1);
        $file->prev();
        $file->prev();
        $closeBrace = $file->current();

        $file->seekMatchingCurlyBrace($closeBrace);

        $this->assertEquals('T_OPEN_CURLY', $file->current()->getName());
        $this->assertEquals(4, $file->current()->getLine());
        $this->assertEquals($closeBrace->getBlockLevel(), $file->current()->getBlockLevel());
    }

    /**
     * @covers spriebsch\PHPca\File::seekTokenId
     * @covers spriebsch\PHPca\File::add
     */
    public function testSeekTokenIdReturnsFalseWhenTokenDoesNotExist()
    {
        $file = new File('filename', 'sourcecode');
        $file->add(new Token(T_OPEN_TAG, '<?php'));
        $file->rewind();

        $this->assertFalse($file->seekTokenId(T_PUBLIC));
    }

    /**
     * @covers spriebsch\PHPca\File::seekToken
     * @covers spriebsch\PHPca\File::add
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testSeekTokenThrowsExceptionWhenTokenDoesNotExist()
    {
        $file = new File('filename', 'sourcecode');
        $file->add(new Token(T_OPEN_TAG, '<?php'));

        $file->rewind();
        $file->seekToken(new Token(T_CLOSE_TAG, '?>'));
    }

    /**
     * @covers spriebsch\PHPca\File::seek
     * @expectedException \OutOfBoundsException
     */
    public function testSeekThrowsExceptionOnInvalidPosition()
    {
        $file = new File('filename', 'sourcecode');
        $file->seek(1);
    }

    /**
     * @covers spriebsch\PHPca\File::seekNamespace
     */
    public function testSeekNamespace()
    {
        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/blocks.php'));
        $file->rewind();

        $file->seekNamespace('B\\C');

        $this->assertEquals('T_OPEN_CURLY', $file->current()->getName());
        $this->assertEquals(18, $file->current()->getLine());

        $file->seekNamespace('A\\B');

        $this->assertEquals('T_OPEN_CURLY', $file->current()->getName());
        $this->assertEquals(4, $file->current()->getLine());
    }

//    /**
//     * @covers spriebsch\PHPca\File::seekClass
//     */
//    public function testSeekClass()
//    {
//        $file = Tokenizer::tokenize('test.php', file_get_contents(__DIR__ . '/_testdata/File/blocks.php'));
//        $file->rewind();
//
//        $file->seekClass('Test');
//
//        $this->assertEquals('T_OPEN_CURLY', $file->current()->getName());
//        $this->assertEquals(6, $file->current()->getLine());
//        $this->assertEquals(5, $file->current()->getColumn());
//    }
}
?>