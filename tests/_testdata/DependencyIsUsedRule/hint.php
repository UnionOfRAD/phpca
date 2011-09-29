<?php

namespace foo\tests\integration\data;

use foo\tests\mocks\data\source\Images;
use foo\tests\mocks\data\source\Galleries;

class Media {

	public function process(Images $images) {
		$gallery = Galleries::create($this->gallery);
	}
}

?>