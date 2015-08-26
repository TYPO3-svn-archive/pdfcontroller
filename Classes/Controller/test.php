<?php

$options = array(
  'http' => array(
    'method' => 'GET',
    'header' => "Cookie: fe_typo3_user=928d875528a5f26a5eb497ea0b9d3f28\r\n"
  ),
);

$url = "http://die-netzmacher.de/news2/?type=67426&FE_SESSION_KEY=928d875528a5f26a5eb497ea0b9d3f28-376dadbedde679e7028e1387a4e09733";
$context = stream_context_create( $options );
$content = file_get_contents( $url, FALSE, $context );
var_dump( __LINE__, $url, $options, $content );
