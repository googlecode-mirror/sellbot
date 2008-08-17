<?php

	/************************* / ChanSelect / *************************/
	$Bot->AddCommand( 'ChanSelect', 'chansel' );
	
	function chansel_RcvTell ( $Params ) {
	
		global $Bot;
		if (!isset($Params[1][1])) {
			$Blob = new ChatBlob(); $Blob->Template( BLOB_CHOOSECHAN );
			$Bot->SendTell( $Params[0], "Choose a channel for your shop by clicking " . $Blob->Output );
			unset($Blob);
		} else {
			$Bot->ChatChannel = $Params[1][1];
			$Bot->ValidateChanMsg();
		}
		
	}
	
?>