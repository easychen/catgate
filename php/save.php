<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: origin, x-requested-with, content-type');
header('add Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');


if( !isset($_REQUEST['content']) ) die( 'NoData' );

$content = trim($_REQUEST['content']);

@mkdir( 'data' );

if( strlen( $content ) > 0 )
{
	$fname = 'data/' . microtime_float() . '.txt';
	file_put_contents( $fname ,  $content );

	die('save to ' . $fname );
}
else
{
	die('empty');
}



function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
