<?php
include_once 'extension/data_import/classes/sourcehandlers/csvhandlers/Folders.php';
include_once 'extension/data_import/classes/importoperators/NoNewVersion.php';

$handler = new Folders();
$operator = new NoNewVersion( $handler );
$operator->run();

?>