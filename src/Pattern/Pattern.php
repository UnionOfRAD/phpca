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

namespace spriebsch\PHPca\Pattern;

/**
 * Represents a token pattern to search a file for.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Pattern implements PatternInterface
{
    protected $items = array();

    /**
     * Make sure that every element in a pattern implements PatternInterface.
     *
     * @param array $patterns
     * @throws spriebsch\PHPca\Pattern\PatternException
     */
    protected function checkType(array $patterns)
    {
        foreach($patterns as $pattern) {
            if (!$pattern instanceOf PatternInterface) {
                throw new PatternException('Pattern expected');
            }
        }
    }

    /**
     * Returns whether the pattern is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return sizeof($this->items) == 0;
    }

    /**
     * Add a pattern
     *
     * @param PatternInterface $pattern
     * @return Pattern
     */
    public function add(PatternInterface $pattern)
    {
        $this->items[] = $pattern;
        return $this;
    }

    /**
     * Add a Token to the pattern
     *
     * @param string $tokenId
     * @return Pattern
     */
    public function token($tokenId)
    {
        return $this->add(new Token($tokenId));
    }

    /**
     * Add a OneOf condition to the pattern
     *
     * @param array $patterns
     * @return Pattern
     */
    public function oneOf(array $patterns)
    {
        $this->checkType($patterns);

        return $this->add(new OneOf($patterns));
    }

    /**
     * Add a OneOrMore condition to the pattern
     *
     * @param array $patterns
     * @return Pattern
     */
    public function oneOrMore(PatternInterface $pattern)
    {
        return $this->add(new OneOrMore($pattern));
    }

    /**
     * Add a ZeroOrMore condition to the pattern
     *
     * @param PattenrInterface $pattern
     * @return Pattern
     */
    public function zeroOrMore(PatternInterface $pattern)
    {
        return $this->add(new ZeroOrMore($pattern));
    }

    /**
     * Returns the regular expression the pattern represents.
     *
     * @return string
     */
    public function getRegEx()
    {
        $result = '';

        for ($i = 0; $i < sizeof($this->items); $i++) {
            $separator = true;
            $part = $this->items[$i]->getRegEx();

            $result .= $part;

            $lastChar = substr($part, -1);
            if ($lastChar == '*' || $lastChar == '+') {
                $separator = false;
            }

            if ($separator && $i < sizeof($this->items) - 1) {
                $result .= ' ';
            }
        }

        return $result;
    }
}
?>