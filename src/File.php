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
 * The File class represents a PHP source file as a sequence of Token objects
 * and provides several means of navigating these Tokens.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @todo clone sub-queue with tokens of a class/function/block?
 * @todo methods to seek to namespace, class or function
 */
class File extends \SplDoublyLinkedList implements \SeekableIterator
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $sourceCode;

    /**
     * @var string
     */
    protected $toString;

    /**
     * Constructs the File object
     *
     * We set the file name and source code to keep File independent
     * from the actual file system. Still, we'll need the file name
     * later when we output the result.
     *
     * @param string $fileName   Source file name
     * @param string $sourceCode The actual source code
     * @return null
     */
    public function __construct($fileName, $sourceCode)
    {
        $this->fileName   = $fileName;
        $this->sourceCode = $sourceCode;
    }

    /**
     * Returns string representation of the object.
     * Result is cached since __toString() is called often.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->toString !== null) {
            return $this->toString;
        }

        $result = array();

        $this->rewind();
        while($this->valid()) {
            $result[] = $this->current()->getName();
            $this->next();
        }

        $result = implode(' ', $result);
        $this->toString = $result;

        return $result;
    }

    /**
     * Returns the file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns the source code
     *
     * @return string
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * Returns an array of all namespaces in the file, excluding the
     * global namespace \ that is always present, even when the whole
     * file has a namespace, since by PHPca convention the namespace
     * statement is always part of the global namespace.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $result = array();

        $this->rewind();

        while($this->valid()) {
            $namespace = $this->current()->getNamespace();
            if ($namespace != '\\' && !in_array($namespace, $result)) {
                $result[] = $namespace;
            }
            $this->next();
        }

        return $result;
    }

    /**
     * Returns an array of all classes in the file.
     *
     * @return array
     */
    public function getClasses()
    {
        $result = array();

        $this->rewind();

        while($this->valid()) {
            $class = $this->current()->getClass();
            if ($class != '' && !in_array($class, $result)) {
                $result[] = $class;
            }
            $this->next();
        }

        return $result;
    }

    /**
     * Returns an array of all methods of the given class in a file.
     * We do not distinguish between static methods and instance methods.
     *
     * @return array
     */
    public function getMethods($class)
    {
        $this->rewind();
    	$result = array();

        while($this->valid()) {
            $function = $this->current()->getFunction();
            if ($this->current()->getClass() == $class && $function != '' && !in_array($function, $result)) {
                $result[] = $function;
            }
            $this->next();
        }

        return $result;
    }

    /**
     * Returns an array of all functions in the file, or an array
     * of all methods of the given class. We do not distinguish between
     * static methods and instance methods.
     *
     * @return array
     */
    public function getFunctions()
    {
        $result = array();

        $this->rewind();

        while($this->valid()) {
            $function = $this->current()->getFunction();
            if ($this->current()->getClass() == '' && $function != '' && !in_array($function, $result)) {
                $result[] = $function;
            }
            $this->next();
        }

        return $result;
    }

    /**
     * Adds a token
     *
     * @param Token $token
     * @return null
     */
    public function add(Token $token)
    {
        $this->toString = null;

        parent::push($token);
    }

    /**
     * Seek to a namespace.
     * Search always starts from the beginning.
     *
     * @param string $namespace
     */
    public function seekNamespace($namespace)
    {
        $this->rewind();

        while($this->valid()) {
            if ($this->current()->getNamespace() == $namespace) {
                return;
            }
            $this->next();
        }

        throw new Exception('No namespace ' . $token->getNamespace() . ' found');
    }

    /**
     * Seek to next line.
     *
     * @return null
     * @todo how to deal with multiline tokens? probably not at all?
     */
    public function seekNextLine()
    {
        $line = $this->current()->getLine();

        while (true) {
            $this->next();

            if (!$this->valid()) {
                return false;
            }

            if ($this->current()->getLine() > $line) {
                return true;
            }
        }
    }

    /**
     * Seek to a class.
     * Search always starts from the beginning.
     *
     * @param string $class
     */
//    public function seekClass($class)
//    {
//        $this->rewind();
//
//        while($this->valid()) {
//            if ($this->current()->getClass() == $class) {
//                return;
//            }
//            $this->next();
//        }
//
//        throw new Exception('No class ' . $token->getClass() . ' found');
//    }

    /**
     * Seek to given token. Will rewind(), so it finds the token
     * regardless of the current position.
     *
     * @param Token $token
     * @return null
     */
    public function seekToken(Token $token)
    {
        $this->rewind();

        while($this->valid()) {
            if ($this->current() === $token) {
                return;
            }
            $this->next();
        }

        throw new Exception('No token ' . $token->getName() . ' found');
    }

    /**
     * Seek to token ID, returning true on success
     * and false if the token is not found.
     * Seeking starts from current element.
     * If the current token has given ID, we do not seek to the next token of
     * that ID.
     *
     * @param int $id
     * @param bool $backwards
     * @return null
     */
    public function seekTokenId($id, $backwards = false)
    {
        $currentPosition = $this->key();

        while($this->valid()) {
            if ($this->current()->getId() == $id) {
                return true;
            }

            if ($backwards) {
                $this->prev();
            } else {
                $this->next();
            }
        }

        $this->seek($currentPosition);
        return false;
    }

    /**
     * Seeks to the matching curly brace.
     *
     * @param Token $token
     */
    public function seekMatchingCurlyBrace(Token $token)
    {
        $id = $token->getId();
        $level = $token->getBlockLevel();

        if ($id != T_OPEN_CURLY && $id != T_CLOSE_CURLY) {
            throw new Exception($token->getName() . ' is not a curly brace');
        }

        // Forward search
        if ($id == T_OPEN_CURLY) {
            $this->next();

            while ($this->valid()) {
                $token = $this->current()->getId();
                $closeLevel = $this->current()->getBlockLevel();

                if ($token == T_CLOSE_CURLY && $closeLevel == $level) {
                    return;
                }

                $this->next();
            }
        }

        // Backward search
        if ($id == T_CLOSE_CURLY) {
            $this->prev();

            while ($this->valid()) {
                $token = $this->current()->getId();
                $openLevel = $this->current()->getBlockLevel();

                if ($token == T_OPEN_CURLY && $level == $openLevel) {
                    return;
                }

                $this->prev();
            }
        }

        // This should be impossible since in a syntactically valid
        // PHP file, every opened curly brace must be closed.
        throw new Exception('No matching curly brace found');
    }

    /**
     * Seek to given index
     *
     * @param int $index
     */
    public function seek($index)
    {
        $this->rewind();
        $position = 0;
        while($position < $index) {
            if (!$this->valid()) {
                throw new \OutOfBoundsException('Invalid seek position');
            }

            $this->next();
            $position++;
        }
    }
}
?>