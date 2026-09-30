<?php

$fp = fsockopen(
    'smtp-relay.brevo.com',
    587,
    $errno,
    $errstr,
    30
);

if(!$fp)
{
    echo "FAILED<br>";
    echo $errno."<br>";
    echo $errstr;
}
else
{
    echo "CONNECTED";
    fclose($fp);
}