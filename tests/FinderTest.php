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
 * Tests for the Finder class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FinderTest extends \PHPUnit_Framework_TestCase
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
     * @covers spriebsch\PHPca\Finder::findToken
     */
    public function testFindTokenReturnsEmptyArrayWhenTokenNotFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $result = Finder::findToken($file, T_CLASS);

        $this->assertEquals(0, sizeof($result));
    }

    /**
     * @covers spriebsch\PHPca\Finder::findToken
     */
    public function testFindToken()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $result = Finder::findToken($file, T_FUNCTION);

        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('T_FUNCTION', $result[0]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::containsToken
     */
    public function testContainsReturnsTrueWhenTokenIsFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $this->assertTrue(Finder::containsToken($file, T_FUNCTION));
    }

    /**
     * @covers spriebsch\PHPca\Finder::containsToken
     */
    public function testContainsReturnsFalseWhenTokenIsNotFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $this->assertFalse(Finder::containsToken($file, T_CLASS));
    }

    /**
     * @covers spriebsch\PHPca\Finder::findRegEx
     */
    public function testFindPattern()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $pattern = new Pattern();
        $pattern->token(T_FUNCTION)
                ->token(T_WHITESPACE)
                ->token(T_STRING)
                ->token(T_OPEN_BRACKET)
                ->token(T_CLOSE_BRACKET)
                ->token(T_WHITESPACE)
                ->token(T_OPEN_CURLY);

        $result = Finder::findPattern($file, $pattern);
var_dump($result);

        // $this->assertEquals(1, sizeof($result));
        // $this->assertEquals('T_FUNCTION', $result[0]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findRegEx
     */
    /*
    public function testFindRegExWithOptionalTokens()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");
        $result = Finder::findRegEx($file, 'T_FUNCTION T_WHITESPACE T_STRING T_OPEN_BRACKET T_CLOSE_BRACKET T_WHITESPACE T_OPEN_CURLY');
var_dump($result);

        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello(\$a, \$b)\n{\n    print 'hello world';\n} \n ?>");
        $result = Finder::findRegEx($file, 'T_FUNCTION T_WHITESPACE T_STRING T_OPEN_BRACKET T_* T_CLOSE_BRACKET T_WHITESPACE T_OPEN_CURLY');
var_dump($result);

        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello(\$a, \$b)\n{\n    print 'hello world';\n} \n ?>");
        $result = Finder::findRegEx($file, 'T_FUNCTION T_WHITESPACE T_STRING T_OPEN_BRACKET T_+ T_CLOSE_BRACKET T_WHITESPACE T_OPEN_CURLY');
var_dump($result);

        // $this->assertEquals(1, sizeof($result));
        // $this->assertEquals('T_FUNCTION', $result[0]->getName());
    }
     */
}
?>