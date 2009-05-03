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

use spriebsch\PHPca\Constants as Constants;

require_once 'PHPUnit/Framework.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Tests for the Constants class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class ConstantsTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers spriebsch\PHPca\Constants::init
    */
    public function testInitDefinesConstants()
    {
        Constants::init();
        $this->assertTrue(array_key_exists('T_DIV', get_defined_constants()));
    }

    /**
    * @covers spriebsch\PHPca\Constants::getTokenId
    */
    public function testGetTokenId()
    {
        Constants::init();
        $this->assertEquals(516, Constants::getTokenId('/'));
    }

    /**
    * @covers spriebsch\PHPca\Constants::getTokenId
    * @expectedException \RuntimeException
    */
    public function testGetTokenIdThrowsExceptionOnUnknownToken()
    {
        Constants::init();
        Constants::getTokenId('does not exist');
    }

    /**
    * @covers spriebsch\PHPca\Constants::getTokenName
    */
    public function testGetTokenName()
    {
        Constants::init();
        $this->assertEquals('T_DIV', Constants::getTokenName(516));
    }

    /**
    * @covers spriebsch\PHPca\Constants::getTokenName
    */
    public function testGetTokenNameReturnsTokenizerToken()
    {
        Constants::init();
        $this->assertEquals('T_DOC_COMMENT', Constants::getTokenName(367));
    }

    /**
    * @covers spriebsch\PHPca\Constants::getTokenName
    * @expectedException \RuntimeException
    */
    public function testGetTokenNameThrowsExceptionOnUnknownToken()
    {
        Constants::init();
        Constants::getTokenName(9999);
    }
}
?>
