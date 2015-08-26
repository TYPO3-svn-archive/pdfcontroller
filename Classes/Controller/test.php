<?php

$url =  'http://intern.reineckervision.digitalgenossen.de/einblickintern/ausgabe-012015-februar-2015/?type=67426&FE_SESSION_KEY=94e14d245f51e55217716a9aac289c26-5e7ffe49d4e2c37c6594a368613ed96b';
// create curl resource
$ch = curl_init();
// set url
curl_setopt( $ch, CURLOPT_URL, $url );
//return the transfer as a string
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
// return without any header
//curl_setopt( $ch, CURLOPT_HEADER, 0 );
// $output contains the output string
$content2 = curl_exec( $ch );

$content = file_get_contents( $url );
var_dump( __METHOD__, __LINE__, $url, $content, $content2 );
exit;
echo $content . PHP_EOL;

