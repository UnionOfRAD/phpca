<?php

namespace foo\bar;

use baz\ClassA;
use baz\ClassB;
use baz\ClassC;
use baz\ClassD;
use baz\ClassE;
use baz\ClassF;

class Mixed extends ClassA implements ClassB {

	public function test() {
		if ($c instanceof ClassC) {
			// ...
		}
		$d = new ClassD();
		$e = ClassE::test();
	}
}

?>