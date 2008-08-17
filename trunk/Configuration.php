<?php

	// Dimension: Choose between Atlantean, Rimor, DNW or Test
	$Dimension = 'Rimor';
	
	// The following settings sort of speak for themselves.
	$AccountName = 'Techboy';
	$AccountPassword = '';
	$ShopCharacter = 'Texon';
	$ShopAdministrator = 'Toxor';

	// Do not touch this, it is here incase chatserver info changes.
	$Servers = Array (
		'Atlantean' => Array('chat.d1.funcom.com',7012),
		'Rimor' => Array('chat.d2.funcom.com', 7012),
		'DNW' => Array('chat.d3.funcom.com',7013),
		'Test' => Array('chat.dt.funcom.com', 7012)
	);
	$Dimension = $Servers[$Dimension];
?>