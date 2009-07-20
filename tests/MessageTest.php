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
 * Tests for the Message class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class MessageTest extends \PHPUnit_Framework_TestCase
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
     * @covers spriebsch\PHPca\Message::getFileName
     */
    public function testGetFileName()
    {
        $msg = new Message('filename', 'message');
        $this->assertEquals('filename', $msg->getFileName());
    }

    /**
     * @covers spriebsch\PHPca\Message::getMessage
     */
    public function testGetMessage()
    {
        $msg = new Message('filename', 'message');
        $this->assertEquals('message', $msg->getMessage());
    }

    /**
     * @covers spriebsch\PHPca\Message::getLine
     */
    public function testGetLine()
    {
        $t = new Token(T_OPEN_TAG, "<?php", 5);
        $msg = new Message('filename', 'message', $t);

        $this->assertEquals(5, $msg->getLine());
    }

    /**
     * @covers spriebsch\PHPca\Message::getLine
     */
    public function testGetLineIsZeroWhenNoTokenGiven()
    {
        $msg = new Message('filename', 'message');
        $this->assertEquals(0, $msg->getLine());
    }

    /**
     * @covers spriebsch\PHPca\Message::getColumn
     */
    public function testGetColumn()
    {
        $t = new Token(T_OPEN_TAG, "<?php", 0, 7);
        $msg = new Message('filename', 'message', $t);

        $this->assertEquals(7, $msg->getColumn());
    }

    /**
     * @covers spriebsch\PHPca\Message::getColumn
     */
    public function testGetColumnIsZeroWhenNoTokenGiven()
    {
        $msg = new Message('filename', 'message');
        $this->assertEquals(0, $msg->getColumn());
    }
}
?>