<?php 

define('EMAIL_ALTERNATIVE','robo@senf.at');

$noreply = 'noreply@'.substr(EMAIL_ALTERNATIVE,strpos(EMAIL_ALTERNATIVE,'@')+1);

var_dump($noreply);