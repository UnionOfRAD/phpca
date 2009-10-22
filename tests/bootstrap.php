<?php

require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

require_once __DIR__ . '/Rule/AbstractRuleTest.php';

spriebsch\PHPca\Loader::init();
spriebsch\PHPca\Loader::registerPath(realpath(__DIR__ . '/../src'));
spriebsch\PHPca\Loader::registerPath(realpath(__DIR__ . '/_testdata/Rule'));

?>