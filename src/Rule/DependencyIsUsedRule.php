<?php

namespace spriebsch\PHPca\Rule;

/**
 * Dependencies delcared via `use` must actually be used inside the file.
 */
class DependencyIsUsedRule extends Rule
{

    /**
     * Performs the rule check
     *
     * @return void
     */
    protected function doCheck()
    {
        $code = $this->file->getSourceCode();

        while ($this->file->seekTokenId(T_USE)) {
            $use = $this->file->current();

            $this->file->next(); // Whitespace.
            $this->file->next();
            $parts = array();

            while ($this->file->current()->getId() !== T_SEMICOLON) {
                $text = $this->file->current()->getText();

                if ($text === '(') {
                    continue(2); // Is part of a closure.
                }

                if ($text !== '\\') {
                    $parts[] = $text;
                }
                $this->file->next();
            }
            $name = end($parts);

            $patterns = array(
                "new {$name}\(",
                "{$name}::",
                "\({$name} \\$",
                "instanceof {$name}(;|:|\))",
                "extends {$name}",
                "implements {$name}"
            );
            if (!preg_match('/(' . implode('|', $patterns) . ')/i', $code)) {
                $qualified = implode('\\', $parts);
                $this->addViolation("Dependency `{$qualified}` declared but not used", $use);
            }
        }
    }
}

?>