<?php
	
	// SellBot
	// By Demerzel, 2008
	
	echo ("\tSellBot\n\tCoded by Demerzel, 2008\n\tLicensed under the GNU General Public License\n\thttp://code.google.com/p/sellbot/\n\n");
	echo ('Loading... ');
	
	/*** Prepare ******/
	include ( 'Assets/AOChat.php' );
	include ( 'Assets/Consts.php' );
	include ( 'Configuration.php' );
	$AOChat = new AOChat('Callback');
	/*** Log In ******/
	echo ('ShopBot is logging in to character ' . $ShopCharacter . ' on account ' . $AccountName . "... \n");
	$AOChat->connect( $Servers[$Dimension][0], $Servers[$Dimension][1] );
	$AOChat->authenticate( $AccountName, $AccountPassword );
	$AOChat->login( $ShopCharacter );
	/*** Ready! ******/
	
	while ($AOChat->get_packet()) {
	}
	
	function Callback($PType, $PArgs) {
	
		global $AOChat, $ShopCharacter, $ShopAdministrator;
		
		switch ($PType) {
			case AOCP_LOGIN_OK:
				echo 'Login Successful.';
				BotSpawn();
				break;
			
			case AOCP_LOGIN_ERROR:
				die( "Login Failed: {$PArgs[0]}" );	
			
			default:
				break;
		}
	
	}
	
	function StartBot() {
		global $AOChat, $ShopCharacter, $ShopAdministrator;
		
	}
	
?>