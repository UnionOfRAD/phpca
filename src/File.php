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
 */
class File extends \SplQueue implements \SeekableIterator
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
     *
     * @return string
     */
    public function __toString()
    {
        $result = array();

        $this->rewind();
        while($this->valid()) {
            $result[] = $this->current()->getName();
            $this->next();
        }

        return implode(' ', $result);
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
     * Adds a token
     *
     * @param Token $token
     * @return null
     */
    public function add(Token $token)
    {
        parent::enqueue($token);
    }

    /**
     * Seek to the next token $id.
     * If the current token has given ID, we do not seek to the next token of
     * that ID.
     *
     * @param int $id
     * @return null
     */
    public function seekToken($id)
    {
        while($this->valid()) {
            if ($this->current()->getId() == $id) {
                return;
            }
            $this->next();
        }

        throw new Exception('Invalid seek token id ' . $id);
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
        while($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }
        if (!$this->valid()) {
            throw new \OutOfBoundsException('Invalid seek position');
        }
    }

// @todo seekto matching brace: when open brace, seek to closing brace of same block level
// when close brace, seek back to open brace of same block level

// @todo potentially offer method that clones "part" of the file (one class/block, for example) and returns a queue of them.

}
?>