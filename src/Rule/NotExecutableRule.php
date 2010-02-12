<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the file is not executable.
 */
class NotExecutableRule extends Rule
{

	protected function skip()
	{
		return !file_exists($this->file->getFileName());
	}

    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
		if (is_executable($this->file->getFileName())) {
			$this->addViolation('File is executable', null, 0 , 0);
		}
	}
}
?>