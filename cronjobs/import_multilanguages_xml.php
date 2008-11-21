<?php
include_once 'extension/data_import/classes/sourcehandlers/xmlhandlers/MultiLanguages.php';
include_once 'extension/data_import/classes/ImportOperator.php';

$handler = new MultiLanguagesHandler();
$operator = new ImportOperator( $handler );
$operator->run();

?>