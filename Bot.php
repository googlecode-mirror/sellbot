<?php
	
	// SellBot
	// By Demerzel, 2008
	
	set_time_limit(0);
	echo ("\n\n\n\n\n");
	echo ("\tSellBot\n\tCoded by Demerzel, 2008\n\tLicensed under the GNU General Public License\n\thttp://code.google.com/p/sellbot/\n\n");
	sleep(5);

	echo ('Loading... ');
	
	/*** Prepare ******/
	
	$ChatGroups = NULL;
	
	include ( 'Assets/AOChat.php' );
	include ( 'Assets/BotClass.php' );
	include ( 'Assets/ChatBlobClass.php' );
	include ( 'Assets/Consts.php' );
	include ( 'Configuration.php' );
	
	$AOChat = new AOChat('Callback');
	
	/*** Intialise ******/
	
	$Bot = new Bot( $AOChat, $ShopCharacter, $ShopAdministrator );
	include ( 'Assets/ChatCmds.php' );
	$Bot->Login( $Dimension, $AccountName, $AccountPassword );
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
				$Bot->ChatGroups[] = $Args;
				break;		
		
			case AOCP_LOGIN_OK:
				echo( "Login Successful.\n\n" );
				break;
			
			case AOCP_LOGIN_ERROR:
				die( "Login Failed: {$Args[0]}" );	
			
			default:
				break;
		}
		
	}
	
?>