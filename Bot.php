<?php
	
	// SellBot
	// By Demerzel, 2008
	
	echo ("\tSellBot\n\tCoded by Demerzel, 2008\n\tLicensed under the GNU General Public License\n\thttp://code.google.com/p/sellbot/\n\n");
	echo ('Loading... ');
	
	/*** Prepare ******/
	
	$ChatGroups = NULL;
	
	include ( 'Assets/AOChat.php' );
	include ( 'Configuration.php' );
	
	$AOChat = new AOChat('Callback');
	
	/*** Intialise ******/
	
	$Bot = new Bot( $AOChat, $Dimension, $AccountName, $AccountPassword, $ShopCharacter, $ShopAdministrator );
	unset($AccountName); unset($AccountPassword);
	
	/*** Ready! ******/
	
	while ($AOChat->get_packet()) {
	}
	
	function Callback($Type, $Args) {
		
		global $ChatGroups, $Bot;
		
		switch ($Type) {
		
			case AOCP_MSG_PRIVATE:
				$Bot->RcvTell( $Args );
				break;
		
			case AOCP_GROUP_ANNOUNCE:
				$ChatGroups[] = $Args;
				break;
				
			case SB_CHANSELECT:
				$Bot->ChooseChatChannel($ChatGroups);
				break;
		
		
			case AOCP_LOGIN_OK:
				echo "Login Successful.\n";
				break;
			
			case AOCP_LOGIN_ERROR:
				die( "Login Failed: {$Args[0]}" );	
			
			default:
				break;
		}
		
	}
	
	class Bot {
	
		var $AOChat, $ShopCharacter, $ShopAdministrator;
		var $ChatGroups = Array();	
		var $ChatChannel = NULL;
		function Bot( &$AOChat, $Dimension, $AccountName, $AccountPassword, $ShopCharacter, $ShopAdministrator ) {
			
			$Servers = Array (
				'Atlantean' => Array('chat.d1.funcom.com',7012),
				'Rimor' => Array('chat.d2.funcom.com', 7012),
				'DNW' => Array('chat.d3.funcom.com',7013),
				'Test' => Array('chat.dt.funcom.com', 7012)
				);

			$this->AOChat = &$AOChat;
			$this->ShopCharacter = $ShopCharacter;
			$this->ShopAdministrator = $ShopAdministrator;
			
			echo ('ShopBot is logging in to character ' . $ShopCharacter . ' on account ' . $AccountName . "... \n");
			$this->AOChat->connect( $Servers[$Dimension][0], $Servers[$Dimension][1] );
			$this->AOChat->authenticate( $AccountName, $AccountPassword );
			$this->AOChat->login( $ShopCharacter );
			
			$this->SendTell($ShopAdministrator, "Your SellBot has logged in successfully. :)");
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
		
		function ChooseChatChannel($ChatGroups) {
			$this->ChatGroups = $ChatGroups;
		
			$Text = "Choose a channel for your shop by clicking "; $Blob = new ChatBlob("here.");
			$Blob->AddText( "Here is a list of all the channels your SellBot can send messages to. Click the one on which you want your shop posted.<br>" );

			foreach ($ChatGroups as $GroupId => $Group){
				$Blob->AddChatCmd( "/tell " . $this->ShopCharacter . " ChanSelect " . $GroupId , $Group[1] );
				$Blob->AddLineBreak();
			}
			$Blob->Render();
			$Text .= $Blob->Output;
			
			$this->SendTell( $this->ShopAdministrator, $Text );
		
		}
		
		function ValidateChanSelect() {
			
			$i = $this->ChatChannel;
		
			/*** Validations *****/
			if ( !isset( $this->ChatGroups[$i] ) ) {
				$this->SendTell ( $this->ShopAdministrator, "Invalid channel ID!" );
				$fail = true;
			} // TODO: This channel cannot be posted on!
			
			if (isset($fail)) {
				$this->ChatChannel = NULL;
				$this->ChooseChatChannel($this->ChatGroups);
			} else {			
				$this->SendTell ( $this->ShopAdministrator, "Channel Selected." );
			}
		}
		
		function RcvTell( $Args ) {
			$CharName = $this->AOChat->get_uname( $Args[0] );
			$Text = $Args[1];
			
			echo "[$CharName]: $Text\n";
			
			$Params = explode(" ", strtolower($Text));
			
			switch ($Params[0]) {
			
				case "chanselect":
					 if (!isset($Params[1]))  Callback(SB_CHANSELECT, NULL); else {
						$this->ChatChannel = $Params[1];
						$this->ValidateChanSelect();
					}
					break;
				
				default:
					$this->SendTell ( $CharName, "Unknown command." );
					
			}
		}
		
		function SendTell( $User, $Text ) {
			$this->AOChat->send_tell( $User, $Text );
			echo "To [$User]: $Text\n"; 
		}
		
	}
	
	class ChatBlob { 
	
		var $Output = NULL, 
			$Title = NULL,
			$Body = NULL;
	
		function AddChatCmd( $Command, $LinkText = "Click Here" ) {
			$this->Body .= '<a href=\'chatcmd://' . $this->Clean($Command) . '\'>' . $LinkText . '</a>';			
		}
		
		function Clean( $Input ) {
			return $Input;
		}
		
		function ChatBlob( $Title = "Click Here" ) { $this->Title = $Title;	}
		
		function AddText( $Text ) { $this->Body .= $Text; }

		function AddLineBreak() { $this->Body .= "<br>"; }
		
		function Render() { $this->Output = '<a href="text://' . $this->Body . '">' . $this->Title . '</a>'; }
	
	}
	
?>