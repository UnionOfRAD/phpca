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
 * Unit Tests for Loader class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @covers spriebsch\MVC\Loader::__construct
     * @expectedException spriebsch\PHPca\CannotInstantiateLoaderException
     */
    public function testConstructorThrowsException()
    {
        $loader = new Loader();
    }

    /**
     * @covers spriebsch\MVC\Loader::init
     */
    public function testInitRegistersSplAutoloadFunction()
    {
        $this->assertContains(array('spriebsch\PHPca\Loader', 'autoload'), spl_autoload_functions());
    }

    /**
     * @covers spriebsch\MVC\Loader::registerPath
     * @expectedException spriebsch\PHPca\ClassMapNotFoundException
     */
    public function testRegisterPathThrowsExceptionWhenClassMapDoesNotExist()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassMapDoesNotExist');
    }

    /**
     * @covers spriebsch\MVC\Loader::registerPath
     * @expectedException spriebsch\PHPca\InvalidClassMapException
     */
    public function testRegisterPathThrowsExceptionWhenClassMapIsNoArray()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassMapNotAnArray');
    }

    /**
     * @covers spriebsch\MVC\Loader::autoload
     */
    public function testLoadLoadsExistingClass()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassA');
        $this->assertTrue(class_exists('spriebsch\PHPca\Tests\A'));
    }

    /**
     * @covers spriebsch\MVC\Loader::autoload
     */
    public function testLoadLoadsExistingClassFromSubdirectory()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassE');
        $this->assertTrue(class_exists('spriebsch\PHPca\Tests\E'));
    }

    /**
     * @covers spriebsch\MVC\Loader::autoload
     */
    public function testLoadLoadsExistingClassInAnotherNamespace()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassF');
        $this->assertTrue(class_exists('some\other\F'));
    }

    /**
     * @covers spriebsch\MVC\Loader::autoload
     */
    public function testLoadWorksForMultipleClassPaths()
    {
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassD');
        Loader::registerPath(__DIR__ . '/_testdata/Loader/ClassesBAndC');
        $this->assertTrue(class_exists('spriebsch\PHPca\Tests\A'));
        $this->assertTrue(class_exists('spriebsch\PHPca\Tests\B'));
    }
}
?>