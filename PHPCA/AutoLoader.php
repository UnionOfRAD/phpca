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

namespace spriebsch\PHPca;

class AutoLoader
{
    /**
    * @var array
    */
    static protected $classPaths = array();

    /**
    * @var array
    */
    static protected $classMaps = array();

    /**
    * Register autoload path.
    * If enoClassMap.php is present in that directory, it is used
    * for class => filename mapping. If no classmap is given,
    * or it is empty, classes are loaded directly from given path.
    *
    * @param string $classPath Path to load classes from
    * @return null
    */
    static public function register($classPath)
    {
        if (substr($classPath, -1) != '/') {
            $classPath .= '/';
        }

        self::$classPaths[] = $classPath;

        if (file_exists($classPath . '/classMap.php')) {
            include $classPath . '/classMap.php';
            self::$classMaps[] = $classMap;
        } else {
            self::$classMaps[] = array();
        }
    }

    /**
    * Initialize the autoloader
    *
    * @return null
    */
    static public function init()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }

        spl_autoload_register(array('spriebsch\PHPca\AutoLoader', 'autoload'));
    }

    /**
    * Determines wether given class is in one of the maps.
    *
    * @param string $class Class name to check for
    * @return boolean
    */
    static public function hasClass($class)
    {
        $count = count(self::$classMaps);

        for ($i = 0; $i < $count; $i++) {
            if (isset(self::$classMaps[$i][$class])) {
                return true;
            }
        }

        return false;
    }

    /**
    * Autoload method
    *
    * Autoloads eno classes from given classpath.
    * Classes are located in the "class" subdirectory.
    *
    * @param string $class Class name to autoload
    * @return null
    */
    static public function autoload($class)
    {
        if (substr($class, 0, 9) != 'spriebsch') {
            return;
        }

        $count = count(self::$classMaps);

        for ($i = 0; $i < $count; $i++) {
            if (isset(self::$classMaps[$i][$class])) {
                include self::$classPaths[$i] . self::$classMaps[$i][$class];
                return true;
            }
        }

        return false;
    }
}
?>
