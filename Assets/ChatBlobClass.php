<?php

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
		
		function ChatBlob( $Title = "Click Here" ) { 
		
			$this->Title = $Title;	
			$this->Body .= "<font color=" . COLOUR_HEADER . ">SellBot 1.0</font> <font color=" . COLOUR_HEADERL . ">Information Screen</font><br><br>"; // Append header
		
		}
		
		function AddText( $Text ) { $this->Body .= $Text; }

		function AddLineBreak() { $this->Body .= "<br>"; }
		
		function Render() { 
	
			global $Bot;
	
			$this->Body .= "<br><br><font color=" . COLOUR_FOOTER . ">Generated by SellBot</font> :: <a href='chatcmd:///tell " . $Bot->ShopCharacter . " About'>More Information</a>";
			$this->Output = '<a href="text://' . $this->Body . '">' . $this->Title . '</a>'; 
		}
	
		function Template( $Type ) {
			global $Bot;
			switch ($Type) {
				case BLOB_ABOUT:
					$this->AddText( "SellBot is a bot developed by Demerzel (RK1: Scottish, RK2: Toxor) ");
					$this->AddText( "designed to enable people to easily create their own personal shop screen. ");
					$this->AddText( "A link to their shop is posted to their selected channel at a specified ");
					$this->AddText( "interval of over five minutes; viewers of the shop can then place offers ");
					$this->AddText( "or reserve items. Auction functions will become available in future versions.<br><br>");
					$this->AddText( "To download SellBot to use for your own shop, visit: <br>" );
					$this->AddChatCmd( "/start http://code.google.com/p/sellbot/", "http://code.google.com/p/sellbot/" );
					$this->Render();
					break;
					
				case BLOB_OPTIONS:
					$this->Title = "Options";
					$this->AddText( "To select the channel your shop will be posted on: " );
					$this->AddChatCmd( "/tell " . $Bot->ShopCharacter . " ChanSelect" );
					$this->Render();
					break;
					
				case BLOB_CHOOSECHAN:
					$this->Title = "here.";
					$this->AddText( "Here is a list of all the channels your SellBot can send messages to. Click the one on which you want your shop posted.<br>" );

					foreach ($Bot->ChatGroups as $GroupId => $Group){
						if ( substr( bin2hex($Group[0]), 0, 2 ) == 86 )  {
							$this->AddChatCmd( "/tell " . $Bot->ShopCharacter . " ChanSelect " . $GroupId, $Group[1] );
							$this->AddLineBreak();
						}
					}
					$this->Render();
					break;
					
				default:
					$this->Title = "[err: unrecognised blob type $Type]";
			}
		}
	
	}
	
?>