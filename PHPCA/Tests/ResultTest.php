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

use spriebsch\PHPca\Result as Result;
use spriebsch\PHPca\Error as Error;
use spriebsch\PHPca\Warning as Warning;

require_once 'PHPUnit/Framework.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Tests for the Result class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers spriebsch\PHPca\Result:getFiles
    */
    public function testGetFiles()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array('testfile'), $result->getFiles());
    }

    /**
    * @covers spriebsch\PHPca\Result:getNumberOfFiles
    */
    public function testGetNumberOfFiles()
    {
        $result = new Result();

        $this->assertEquals(0, $result->getNumberOfFiles());

        $result->addFile('testfile');

        $this->assertEquals(1, $result->getNumberOfFiles());
    }

    /**
    * @covers spriebsch\PHPca\Result:hasWarnings
    */
    public function testHasWarnings()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasWarnings());

        $result->addMessage(new Warning('testfile', 'warning message'));

        $this->assertTrue($result->hasWarnings());
    }

    /**
    * @covers spriebsch\PHPca\Result:hasWarnings
    */
    public function testHasWarningsForFile()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasWarnings('testfile'));

        $result->addMessage(new Warning('testfile', 'warning message'));

        $this->assertTrue($result->hasWarnings('testfile'));
    }

    /**
    * @covers spriebsch\PHPca\Result::getWarnings
    */
    public function testGetWarnings()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array(), $result->getWarnings('testfile'));

        $error = new Error('testfile', 'error message');

        $warning1 = new Warning('testfile', 'a warning');
        $warning2 = new Warning('testfile', 'another warning');

        $result->addMessage($error);
        $result->addMessage($warning1);
        $result->addMessage($warning2);

        $this->assertEquals(array($warning1, $warning2), $result->getWarnings('testfile'));
    }

    /**
    * @covers spriebsch\PHPca\Result:getNumberOfWarnings
    */
    public function testGetNumberOfWarnings()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(0, $result->getNumberOfWarnings());

        $result->addMessage(new Warning('testfile', 'warning'));

        $this->assertEquals(1, $result->getNumberOfWarnings());
    }

    /**
    * @covers spriebsch\PHPca\Result:hasErrors
    */
    public function testHasErrors()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasErrors());

        $result->addMessage(new Error('testfile', 'error message'));

        $this->assertTrue($result->hasErrors());
    }

    /**
    * @covers spriebsch\PHPca\Result:hasErrors
    */
    public function testHasErrorsForFile()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasErrors('testfile'));

        $result->addMessage(new Error('testfile', 'error message'));

        $this->assertTrue($result->hasErrors('testfile'));
    }

    /**
    * @covers spriebsch\PHPca\Result::getErrors
    */
    public function testGetErrors()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array(), $result->getErrors('testfile'));

        $error1 = new Error('testfile', 'error message');
        $error2 = new Error('testfile', 'another error message');

        $warning = new Warning('testfile', 'a warning');

        $result->addMessage($error1);
        $result->addMessage($warning);
        $result->addMessage($error2);

        $this->assertContains($error1, $result->getErrors('testfile'));
        $this->assertContains($error2, $result->getErrors('testfile'));
        $this->assertEquals(2, count($result->getErrors('testfile')));
    }

    /**
    * @covers spriebsch\PHPca\Result:getNumberOfErrors
    */
    public function testGetNumberOfErrors()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(0, $result->getNumberOfErrors());

        $result->addMessage(new Error('testfile', 'error message'));

        $this->assertEquals(1, $result->getNumberOfErrors());
    }
}
?>
