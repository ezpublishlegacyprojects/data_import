<?php

class XmlHandler extends SourceHandler
{
	var $first_row = true;
	var $first_field = true;
	var $idPrepend = 'xml_import_';
	var $handlerTitle = 'Abstract XML Handler';

	function XmlHandler() {}

	function getNextRow()
	{
		$this->first_field = true;
		$this->node_priority = false;
		
		if( $this->first_row )
		{
			$this->first_row = false;
			$this->current_row = $this->data->first_child();
		}
		else
		{
			$this->current_row = $this->current_row->next_sibling();
		}

		if( $this->current_row->type != 1 ) //ignore xml #text nodes
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
			$this->current_field = $this->current_row->first_child();
		}
		else
		{
			$this->current_field = $this->current_field->next_sibling();
		}

		if( $this->current_field->type != 1 ) //ignore xml #text nodes
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
				$node = $node->next_sibling();

			if( !is_object( $node ) )
			{
				$eof = true;
				$node = false;
			}
		} while ( !$eof && $node->type != 1 );

		return $node;
	}
	
	function parse_xml_document( $file, $start_xml_tag )
	{
		// XML, usually from a file
		$inXML = $this->read_file( $file );
		
		//var_dump( $this->is_utf8($inXML) );
		
		if (!$dom = domxml_open_mem( $inXML ))
		{
			die( 'Error while parsing the document'."\n" );
		}

		$elements = $dom->get_elements_by_tagname( $start_xml_tag );
		
		if( !$elements[0] )
		{
			die( 'Could not get a starting xml tag' );
		}
		
		$this->data = $elements[0];
		
		return true;
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

	function updatePublished($eZ_object)
	{
		$db = eZDB::instance();

		$return_unix_ts = false;
		
		$year_month = $this->current_row->get_attribute('issue_date');
		$parts = explode('-', $year_month );
		
		if( count($parts) && $parts[0] == '0' && $parts[1] == '00')
		{
			$return_unix_ts = mktime(0,0,1,1,1,2000);
		}
		elseif( count( $parts ) == 2 )
		{
			$return_unix_ts = mktime(0,0,1,$parts[1],1,$parts[0]);
		}

		if( $return_unix_ts )
		{
			$sql = 'UPDATE ezcontentobject SET published = "'.$return_unix_ts.'", modified = "'.$return_unix_ts.'" where id = "'.$eZ_object->attribute('id').'"';
			
			$db->begin();
			$db->query($sql);
			$db->commit();
			
			include_once( 'kernel/classes/ezcontentcachemanager.php' );
			eZContentCacheManager::clearContentCache( $eZ_object->attribute('id') );
		}

		
		return false;
	}
		
}

?>
