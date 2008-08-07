<?php
	class Bot {
	
		var $AOChat, $ShopCharacter, $ShopAdministrator;
		var $ChatGroups = Array();	
		var $ChatChannel = NULL;
		var $Status = Array();
		function Bot( &$AOChat, $ShopCharacter, $ShopAdministrator ) {
			$this->AOChat = &$AOChat;
			$this->ShopCharacter = $ShopCharacter;
			$this->ShopAdministrator = $ShopAdministrator;
		}
		
		function Login( $Dimension, $AccountName, $AccountPassword ) {
			$Servers = Array (
				'Atlantean' => Array('chat.d1.funcom.com',7012),
				'Rimor' => Array('chat.d2.funcom.com', 7012),
				'DNW' => Array('chat.d3.funcom.com',7013),
				'Test' => Array('chat.dt.funcom.com', 7012)
			);

		
			echo ('ShopBot is logging in to character ' . $this->ShopCharacter . ' on account ' . $AccountName . "... \n");
			$this->AOChat->connect( $Servers[$Dimension][0], $Servers[$Dimension][1] );
			$this->AOChat->authenticate( $AccountName, $AccountPassword );
			$this->AOChat->login( $this->ShopCharacter );
			
			$this->SendTell($this->ShopAdministrator, "Your SellBot has logged in successfully. :)");
			$this->LogonOptions();
		}
		
		function LogonOptions() {
			$Text = "For options, ";
			
			$Blob = new ChatBlob("click here.");
			$Blob->AddText( "To select the channel your shop will be posted on: " );
			$Blob->AddChatCmd( "/tell " . $this->ShopCharacter . " ChanSelect" );
			$Blob->Render();
			
			$Text .= $Blob->Output;
			
			$this->SendTell($this->ShopAdministrator, $Text);
		}
		
		function ChooseChatChannel() {
			//$this->ChatGroups = $ChatGroups;
		
			$Text = "Choose a channel for your shop by clicking "; $Blob = new ChatBlob("here.");
			$Blob->AddText( "Here is a list of all the channels your SellBot can send messages to. Click the one on which you want your shop posted.<br>" );

			foreach ($this->ChatGroups as $GroupId => $Group){
				if ($this->ValidateOutputChan($Group)) {
					$Blob->AddChatCmd( "/tell " . $this->ShopCharacter . " ChanSelect " . $GroupId , $Group[1] );
					$Blob->AddLineBreak();
				}
			}
			$Blob->Render();
			$Text .= $Blob->Output;
			
			$this->SendTell( $this->ShopAdministrator, $Text );
		
		}
		
		function ValidateOutputChan($Group) {
			return (substr(bin2hex($Group[0]), 0, 2) != 86) ? false : true; // first byte is not hex 86 then it is not a shopping channel
		}
		
		function ValidateChanMsg() {
			
			$i = $this->ChatChannel;
		
			/*** Validations *****/
			if ( !isset( $this->ChatGroups[$i] ) ) {
				$this->SendTell ( $this->ShopAdministrator, 'Invalid channel ID!' );
				$fail = true;
			} elseif ( !$this->ValidateOutputChan( $this->ChatGroups[$i] ) ) {
				$this->SendTell ( $this->ShopAdministrator, 'This channel is not a shopping channel!' );
				$fail = true;
			}
			
			if (isset($fail)) {
				$this->ChatChannel = NULL;
				$this->ChooseChatChannel($this->ChatGroups);
			} else {			
				$this->SendTell ( $this->ShopAdministrator, "Channel " . $this->ChatGroups[$i][1] . ' Selected.' );
			}
		}
		
		function RcvTell( $Args ) {
			$CharName = $this->AOChat->get_uname( $Args[0] );
			$Text = $Args[1];
			
			echo "[$CharName]: $Text\n";
			
			$Params = explode(" ", strtolower($Text));
			
			switch (strtolower($Params[0])) {
			
				case "chanselect":
					if (!isset($Params[1])) { $this->ChooseChatChannel(); } else {
						$this->ChatChannel = $Params[1];
						$this->ValidateChanMsg();
					}
					break;
				
				case "about":
					$this->SendTell ( $CharName, "About :: " . $this->GenerateBlob('about') );
					break;
				
				default:
					$this->SendTell ( $CharName, "Unknown command." );
					
			}
		}
		
		function SendTell( $User, $Text ) {
			$this->AOChat->send_tell( $User, $Text );
			echo "To [$User]: $Text\n"; 
		}
		
		function GenerateBlob ( $type ) {
			switch ($type) {
				case 'about':
					$Blob = new ChatBlob();
					$Blob->AddText( "SellBot is a bot developed by Demerzel (RK1: Scottish, RK2: Toxor) ");
					$Blob->AddText( "designed to enable people to easily create their own personal shop screen. ");
					$Blob->AddText( "A link to their shop is posted to their selected channel at a specified ");
					$Blob->AddText( "interval of over five minutes; viewers of the shop can then place offers ");
					$Blob->AddText( "or reserve items. Auction functions will become available in future versions.<br><br>");
					$Blob->AddText( "To download SellBot to use for your own shop, visit: <br>" );
					$Blob->AddChatCmd( "/start http://code.google.com/p/sellbot/", "http://code.google.com/p/sellbot/" );
					$Blob->Render();
					return $Blob->Output;

			}
		}
		
	}
?>