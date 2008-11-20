<?php
include_once 'extension/data_import/classes/SourceHandler.php';

class XmlHandlerPHP5 extends SourceHandler
{
	var $first_row = true;
	var $first_field = true;
	var $idPrepend = 'xml_import_';
	var $handlerTitle = 'Abstract XML Handler';
	var $source_file;
	var $dom;

	function XmlHandlerPHP5() {}

	function getNextRow()
	{
		$this->first_field = true;
		$this->node_priority = false;
		
		if( $this->first_row )
		{
			$this->first_row = false;
			$this->current_row = $this->data->firstChild;
		}
		else
		{
			$this->current_row = $this->current_row->nextSibling;
		}

		if( $this->current_row->nodeType != 1 ) //ignore xml #text nodes
		{
			$this->current_row = $this->getNextValidNode( $this->current_row );
		}

		return $this->current_row;
	}

	function getNextField()
	{
		if( $this->first_field )
		{
			$this->first_field = false;
			$this->current_field = $this->current_row->firstChild;
		}
		else
		{
			$this->current_field = $this->current_field->nextSibling;
		}

		if( $this->current_field->nodeType != 1 ) //ignore xml #text nodes
		{
			$this->current_field = $this->getNextValidNode( $this->current_field );
		}

		return $this->current_field;
	}

	function geteZAttributeIdentifierFromField()
	{
		return 'eZ Attribute Identifier';
	}

	function getValueFromField()
	{
		return 'my fromString value - see documentation';
	}

	function post_publish_handling( $eZ_object, $force_exit = false )
	{
		// in case it is necessary
		return true;
	}

	function getNextValidNode( $node )
	{
		$eof = false;

		do
		{
			if($node)
				$node = $node->nextSibling;
			
			if( !is_object( $node ) )
			{
				$eof = true;
				$node = false;
			}
		} while ( !$eof && $node->nodeType != 1 );
				
		return $node;
	}
	
	function parse_xml_document( $file, $start_xml_tag )
	{
		// XML, usually from a file
		$inXML = $this->read_file( $file );
		
		if (!$this->dom = $this->XmlLoader( $inXML ))
		{
			die( 'Error while parsing the document'."\n" );
		}
		
		$elements = $this->dom->getElementsByTagName( $start_xml_tag );

		if( !$elements->item(0) )
		{
			die( 'Could not get a starting xml tag. (<'.$start_xml_tag.'>) Or XML DOM structure not valid.' );
		}
		
		$this->data = $elements->item(0);
		
		return true;
	}
	
	function HandleXmlError($errno, $errstr, $errfile, $errline)
	{
	    if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
	    {
	        throw new DOMException($errstr);
	    }
	    else
	        return false;
	}
	
	function XmlLoader($strXml)
	{
	    set_error_handler('XmlHandlerPHP5::HandleXmlError');
	    $dom = new DOMDocument();
	    $dom->loadXml($strXml);   
	    restore_error_handler();
	    return $dom;
	}
	
	function is_utf8($string) {
	   
	    // From http://w3.org/International/questions/qa-forms-utf-8.html
	    return preg_match('%^(?:
	          [\x09\x0A\x0D\x20-\x7E]            # ASCII
	        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	    )*$%xs', $string);
	   
	} // function is_utf8

	function updatePublished( $eZ_object, $created, $modified )
	{
		$eZ_object->setAttribute('published', $created);
		$eZ_object->setAttribute('modified', $modified);
		//$eZ_object->store();
		
		return false;
	}

	function setSourceFile( $src )
	{
		$this->source_file = $src;
	}
		
}

?>
