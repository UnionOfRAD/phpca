<?php

namespace foo\tests\integration\data;

use foo\data\Database;
use foo\data\Connections;
use foo\data\model\Query;
use foo\tests\mocks\data\source\Images;
use foo\tests\mocks\data\source\Galleries;

class DatabaseTest extends \lithium\test\Integration {

	public $gallery = array(
		'name' => 'Foo Gallery'
	);

	public function skip() {
		$isDatabase = Connections::get('test') instanceof Database;
	}

	public function testCreateData() {
		$gallery = Galleries::create($this->gallery);
	}

	public function testManyToOne() {
		$query = new Query();
		$images = Images::find('all', $opts + array('with' => 'Galleries'))->data();
	}
}

?>