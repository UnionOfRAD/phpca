<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the namespace declaration comes immediately following
 * the first php opening tag and separated by a space.
 */
class NamespaceComesAfterOpeningAndNewlineRule extends Rule
{

    protected function skip() {
        return !$this->file->getNamespaces();
    }

    /**
     * Performs the rule check
     *
     * @return void
     */
    protected function doCheck()
    {
        if ($this->file->seekTokenId(T_NAMESPACE)) {
            $token = $this->file->current();

            $this->file->prev();
            $before = $this->file->current();

            if ($before->getId() != T_WHITESPACE) {
                $this->addViolation("Namespace not preceded by empty line", $token);
            }

            $this->file->prev();
            if ($before = $this->file->current()) {
                if ($before->getId() != T_OPEN_TAG) {
                    $this->addViolation("Namespace not declared directly after open tag", $token);
                }
            }

            if ($this->file[1]->getLine() != $token->getLine()) {
                $this->addViolation("Namespace not declared directly after the first open tag", $token);
            }

        }
    }
}

?>