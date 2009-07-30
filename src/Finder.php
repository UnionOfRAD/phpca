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

use spriebsch\PHPca\Pattern\PatternInterface;

/**
 * Finds tokens in a File.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @todo This class might have to become FileFinder, if we also need a Finder for a plain array of Token objects
 */
class Finder
{
    /**
     * Convert the space-separated T_* string representation to
     * an array of Token objects. Since these link back to File,
     * they can be used as a basis for traversing file.
     *
     * @param <type> $file
     * @param <type> $matches
     * @return <type>
     */
    static protected function toTokens(File $file, $matches)
    {
        $tokens = (string) $file;
        $result = array();

        foreach ($matches as $match) {

            // To relate the string representation back to the token stream,
            // we find the position of the match in the string representation
            // of file, and count the spaces to find at which token in the
            // sequence the match starts.
            $tokenPos = substr_count(substr($tokens, 0, strpos($tokens, $match)), ' ');

            // The numnber of tokens the match contains is the number of spaces
            // plus one.
            $length = substr_count($match, ' ') + 1;

            $sequence = array();

            for ($i = $tokenPos; $i < $tokenPos + $length; $i++) {
                $sequence[] = $file[$i];
            }

            $result[] = $sequence;
        }

        return $result;
    }

    static public function findToken(File $file, $tokenId)
    {
        $result = array();

        foreach(new TokenFilterIterator($file, $tokenId) as $item) {
            $result[] = $item;
        }

        return $result;
    }

    static public function containsToken(File $file, $tokenId)
    {
        return sizeof(self::findToken($file, $tokenId)) > 0;
    }

    static public function findPattern(File $file, PatternInterface $pattern)
    {
        if ($pattern->isEmpty()) {
            throw new EmptyPatternException('Pattern is empty');
        }

        // Match against the T_X ... T_Z token stream that File::__toString() returns.
        preg_match_all('/' . $pattern->getRegEx() . '/U', (string) $file, $matches);

        // Convert T_A ... T_B match back to array of Token objects.
        return self::toTokens($file, $matches[0]);
    }
}
?>