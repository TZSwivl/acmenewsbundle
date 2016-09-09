<?php

if(isset($_ENV['BOOTSTRAP_PHPUNIT_RECREATE_DB']) && $_ENV['BOOTSTRAP_PHPUNIT_RECREATE_DB'] === true) {
    passthru(sprintf(
        'php %s/console doctrine:database:drop --force --env=test',
        __DIR__ . '/../bin'
    ));
    passthru(sprintf(
        'php %s/console doctrine:database:create --env=test',
        __DIR__ . '/../bin'
    ));
    passthru(sprintf(
        'php %s/console doctrine:schema:update --force --env=test',
        __DIR__ . '/../bin'
    ));
}

require_once __DIR__ . '/autoload.php';