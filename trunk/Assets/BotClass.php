<?php
	class Bot {
	
		var $AOChat, $ShopCharacter, $ShopAdministrator;
		var $ChatGroups = Array();	
		var $ChatChannel = NULL;
		var $Status = Array();
		var $Users = Array();
		var $ChatCmds = Array();
		
		function Bot( &$AOChat, $ShopCharacter, $ShopAdministrator ) {
			$this->AOChat = &$AOChat;
			$this->ShopCharacter = $ShopCharacter;
			$this->ShopAdministrator = $ShopAdministrator;
		}
		
		function Login( $Dimension, $AccountName, $AccountPassword ) {
		
			echo ('ShopBot is logging in to character ' . $this->ShopCharacter . ' on account ' . $AccountName . "... \n");
			$this->AOChat->connect( $Dimension[0], $Dimension[1] );
			$this->AOChat->authenticate( $AccountName, $AccountPassword );
			$this->AOChat->login( $this->ShopCharacter );
			
			$Blob = new ChatBlob(); $Blob->Template( BLOB_OPTIONS );			
			$this->SendTell($this->ShopAdministrator, "Your SellBot has logged in successfully. :: " . $Blob->Output); unset($Blob);
			
		}
		
		function AddCommand( $Command, $Prefix, $Auth = AUTH_ANYBODY ) {
			$this->ChatCmds[$Command] = Array( $Prefix, $Auth );
		}
		
		function RcvTell( $Args ) {
		
			$CharName = $this->AOChat->get_uname( $Args[0] );
			$Text = strip_tags($Args[1]);
			echo "[$CharName]: $Text\n"; 

			if ((strtolower(substr($Text, 0, 15)) == "unknown command") ||
				(strtolower(substr($Text, strlen($CharName)+1, 6)) == "is afk") || 
				(strtolower(substr($Text, strlen($CharName)+1, 7)) == "is away") ||
				(strtolower(substr($Text, 0, 5)) == "/tell") )
				return;
			
			$Params[0] = $CharName;
			$Params[1] = explode(" ", strtolower($Text)); 
			
			$Recognised = false;
			foreach ($this->ChatCmds as $Command=>$Settings) {
				// TODO: Auth Level check!
				if (strtolower($Params[1][0]) == strtolower($Command)) {
					call_user_func($Settings[0] . '_RcvTell', $Params);
					$Recognised = true;
				}
			}

			if ($Recognised == false) {
				switch (strtolower($Params[1][0])) {				
					case "about":
						$Blob = new ChatBlob(); $Blob->Template( BLOB_ABOUT );
						$this->SendTell( $CharName, "About :: " . $Blob->Output );
						unset($Blob);
						break;
					
					default:
						$this->SendTell( $CharName, "Unknown command." );
				}
			}
		}
		
		function SendTell( $User, $Text ) {
			$this->AOChat->send_tell( $User, $Text );
			echo "To [$User]: " . strip_tags($Text) . "\n"; 
		}
		
		function ValidateChanMsg() {
			
			$i = $this->ChatChannel;
		
			/*** Validations *****/
			if ( !isset( $this->ChatGroups[$i] ) ) {
				$this->SendTell( $this->ShopAdministrator, 'Invalid channel ID!' );
				$fail = true;
			} elseif ( substr(bin2hex($this->ChatGroups[$i][0]), 0, 2) != 86 ) {
				$this->SendTell( $this->ShopAdministrator, 'This channel is not a shopping channel!' );
				$fail = true;
			}
			
			if (isset($fail)) {
				$this->ChatChannel = NULL;
				$Blob = new ChatBlob(); $Blob->Template( BLOB_CHOOSECHAN );
				$this->SendTell( $this->ShopAdministrator, "Choose a channel for your shop by clicking " . $Blob->Output ); unset($Blob);
			} else {			
				$this->SendTell( $this->ShopAdministrator, "Channel " . $this->ChatGroups[$i][1] . ' Selected.' );
			}
			
		}
		
	}
?>