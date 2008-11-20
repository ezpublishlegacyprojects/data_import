<?php

include_once('extension/data_import_v2/cronjobs/include.php');

$args = $_SERVER['argv'];

$handlerName = false;

if(!empty($args[2]))
{
	$handlerName = $args[2];
}
if($handlerName)
{
	$handler = new $handlerName();
	$operator = new RemoveOperator( $handler );
	$operator->run();
}

?>
