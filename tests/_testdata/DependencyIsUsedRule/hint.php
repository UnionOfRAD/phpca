<?php

namespace foo\tests\integration\data;

use foo\tests\mocks\data\source\Images;
use foo\tests\mocks\data\source\Videos;
use foo\tests\mocks\data\source\Galleries;
use foo\tests\mocks\data\source\Camera;
use foo\Exception;

class Media {

	public function process(Images $images) {
		$gallery = Galleries::create($this->gallery);

		$all = function(Videos $videos) {
			return 'all';
		};
	}

    public function source($param1, Camera $param2) {
        try {

        } catch (Exception $e) {

        }
    }
}

?>