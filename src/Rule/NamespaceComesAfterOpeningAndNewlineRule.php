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
        $this->file->seekTokenId(T_NAMESPACE);
        $token = $this->file->current();

        $this->file->prev();
        $current = $this->file->current();
        $lines = 0;

        if ($current->getId() != T_WHITESPACE) {
            $this->addViolation("Namespace not preceded by empty line", $token);
        }

        while ($current && $current->getId() != T_OPEN_TAG) {
            if ($current->getId() == T_WHITESPACE) {
                $lines++;
            }
            if ($lines != 1 && $current->getId() != T_DOC_COMMENT) {
                $this->addViolation("Namespace not declared directly after open tag", $token);
                break;
            }
            $this->file->prev();
            $current = $this->file->current();
        }

        if ($current->getId() == T_OPEN_TAG) {
            $this->file->rewind();
            $this->file->seekTokenId(T_OPEN_TAG);
            $first = $this->file->current();
            $this->file->seekTokenId(T_OPEN_TAG);

            if ($current->getLine() != $first->getLine()) {
                $this->addViolation("Namespace not declared directly after the first open tag", $token);
            }
        }
    }
}

?>