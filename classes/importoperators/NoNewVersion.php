<?php
include_once 'extension/data_import/classes/ImportOperator.php';

class NoNewVersion extends ImportOperator
{

	function NoNewVersion( $handler )
	{
		parent::ImportOperator( $handler );
	}

	function update_eZ_node( $remoteID, $row, $targetContentClass, $targetLanguage = null )
	{
		$this->do_publish = false;
	
		$this->current_eZ_version = $this->current_eZ_object;
		
		return true;
	}
}

?>