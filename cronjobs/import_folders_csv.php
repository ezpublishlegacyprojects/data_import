<?php
include_once 'extension/data_import/classes/sourcehandlers/csvhandlers/Folders.php';
include_once 'extension/data_import/classes/ImportOperator.php';

$handler = new Folders();
$operator = new ImportOperator( $handler );
$operator->run();

?>