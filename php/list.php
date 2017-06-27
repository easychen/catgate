<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: origin, x-requested-with, content-type');
header('add Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');


$url = array();

for( $i = 1 ; $i <= 5 ; $i++ )
	$url[] = 'https://m.weibo.cn/api/container/getIndex?containerid=2302591088413295&page='.$i;

header("Content-type:application/json");
echo json_encode( $url );