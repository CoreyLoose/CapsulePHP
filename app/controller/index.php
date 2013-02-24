<?php
$default_ui = capsule()->usePattern('DefaultUI');

capsule()->output->setTitle('Welcome to CapsulePHP');

$default_ui->draw(
    capsule()->template->get('welcome')
);