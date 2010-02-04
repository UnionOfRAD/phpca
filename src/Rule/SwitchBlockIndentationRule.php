<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the control portions of switch blocks follows a consistent
 * indent pattern, such that the closing break statement should be indented
 * to the same level as the corresponding opening case statement.
 */
class SwitchBlockIndentationRule extends Rule {

	/**
	 * Performs the rule check
	 *
	 * @return void
	 */
	protected function doCheck() {
		while ($this->file->valid()) {

			$token = $this->file->current();

			$this->file->next();
			if($this->file->valid()) {
				if ($this->file->current()->getId() == T_BREAK) {
					$blockLevel = $token->getBlockLevel();
					$this->file->prev();

		            $indentString = str_repeat('	', $blockLevel);
		            $fileIndentation = $token->getTrailingWhitespace();

		            if ($fileIndentation != $indentString) {
		                 $this->addViolation("Inconsistent Indentation: `break` doesn't correspond to `case`", $this->file->current());
		            }

			        if (!$this->file->seekNextLine()) {
		                return;
		            }
				}
			}
		}
	}
}

?>