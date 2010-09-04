<?php

$phar = new Phar('_logs/phar/phpca.phar');
$phar->buildFromDirectory('src');
$phar->setStub(file_get_contents('PharStub.php'));

?>
