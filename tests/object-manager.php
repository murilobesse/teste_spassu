<?php

// tests/object-manager.php

require __DIR__.'/../vendor/autoload.php';

$kernel = new App\Kernel('test', true);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
