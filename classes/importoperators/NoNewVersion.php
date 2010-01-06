<?php

class NoNewVersion extends ImportOperator
{
	protected function update_eZ_node( $remoteID, $row, $targetContentClass, $targetLanguage = null )
	{
		$this->do_publish = false;
	
		$this->current_eZ_version = $this->current_eZ_object;
		
		return true;
	}
}

?>