<?php

include 'vendor/autoload.php';

use Alxche\Cipher\StudyBegin;

$test = new StudyBegin();
$test->setKey('AUDIO')->hkdfKey('sha256', 112)->splitKeyExpanded();
print_r($test->showProp());