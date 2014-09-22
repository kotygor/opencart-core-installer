<?php
// This is global bootstrap for autoloading
Codeception\Util\Autoload::register(
    'Etki\Composer\Installers\Opencart\Tests\Support',
    '',
    __DIR__ . DIRECTORY_SEPARATOR . '_support'
);