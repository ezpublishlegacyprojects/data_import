<?php
include_once 'extension/data_import/classes/sourcehandlers/xmlhandlers/Images.php';
include_once 'extension/data_import/classes/ImportOperator.php';

$handler = new Images();
$operator = new ImportOperator( $handler );
$operator->run();

?>