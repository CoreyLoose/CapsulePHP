<?php
require __DIR__.'/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', '');

$lib = new HTMLPurifier($config);