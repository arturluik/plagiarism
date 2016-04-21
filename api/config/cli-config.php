<?php
// Doctrine cli-config

require __DIR__ .  '/../bootstrap.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
