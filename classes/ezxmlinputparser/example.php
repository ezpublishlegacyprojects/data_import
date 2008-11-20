<?php

include_once( 'kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinputparser.php' );

class Example_eZInputParser extends eZSimplifiedXMLInputParser
{
	function Example_eZInputParser( $contentObjectID, $validateErrorLevel = eZXMLInputParser::ERROR_ALL, $detectErrorLevel = eZXMLInputParser::ERROR_ALL,
                                         $parseLineBreaks = false, $removeDefaultAttrs = false )
	{
		parent::eZSimplifiedXMLInputParser( $contentObjectID, $validateErrorLevel = eZXMLInputParser::ERROR_ALL, $detectErrorLevel = eZXMLInputParser::ERROR_ALL,
		                                    $parseLineBreaks = false, $removeDefaultAttrs = false );
		
	}
}
?>
