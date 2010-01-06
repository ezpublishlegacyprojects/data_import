<?php

#################
#  Setting up env
#################

require 'autoload.php';

if ( file_exists( "config.php" ) )
{
    require "config.php";
}

$params = new ezcConsoleInput();

$helpOption = new ezcConsoleOption( 'h', 'help' );
$helpOption->mandatory = false;
$helpOption->shorthelp = "Show help information";
$params->registerOption( $helpOption );

$source_handler_option = new ezcConsoleOption( 's', 'source_handler', ezcConsoleInput::TYPE_STRING );
$source_handler_option->mandatory = true;
$source_handler_option->shorthelp = "The source handler class name.";
$params->registerOption( $source_handler_option );

$import_operator_option = new ezcConsoleOption( 'i', 'import_handler', ezcConsoleInput::TYPE_STRING );
$import_operator_option->mandatory = true;
$import_operator_option->shorthelp = "The import handler class name.";
$params->registerOption( $import_operator_option );

// Process console parameters
try
{
    $params->process();
}
catch ( ezcConsoleOptionException $e )
{
    print( $e->getMessage(). "\n" );
    print( "\n" );

    echo $params->getHelpText( 'data_import run script.' ) . "\n";

    echo "\n";
    exit();
}

####################
# Script process
####################

$source_handler_id  = $source_handler_option->value;
$import_operator_id = $import_operator_option->value;

# work with class autoloads or include your classes here

$source_handler = new $source_handler_id;

if( class_exists( $source_handler_id ) )
{

	if( class_exists( $import_operator_id ) )
	{
		$import_operator = new $import_operator_id;

		$import_operator->source_handler = $source_handler;
		$import_operator->run();
	}
	else
	{
		echo 'Could not get an instance of the import operator ( '. $import_operator_id .' ).' . "\n";
	}

}
else
{
	echo 'Could not get an instance of the source handler ( '. $source_handler_id .' ).' . "\n";
}

?>