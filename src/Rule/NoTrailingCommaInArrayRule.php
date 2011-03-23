<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace spriebsch\PHPca\Rule;

/**
 * Checks if the last element of an array has a trailing comma.
 */
class NoTrailingCommaInArrayRule extends Rule
{
    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        while ($this->file->seekTokenId(T_COMMA)) {
            $comma = $this->file->current();
            $this->file->next();

            while ($this->file->current()->getId() === T_WHITESPACE) {
                $this->file->next();
            }
            $token = $this->file->current();

            if ($token->getId() === T_CLOSE_BRACKET) {
                $this->addViolation('Comma after last array element', $comma);
            }
        }
    }
}
?>