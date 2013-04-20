<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title></title>
	</head>
	<body>
	
	<?php

	//'RQ156150817SG';
	include('simple_html_dom.php');
	include('get_status_functions.php');
	$itemcode = isset($_POST['itemcode'])?$_POST['itemcode']:false;

	if ($itemcode) {
		
		$itemcodeArr = array();
		$itemcodeArr = preg_split("/\r\n/",$itemcode,-1,PREG_SPLIT_NO_EMPTY);
		$itemcodeArr = array_unique($itemcodeArr);

		foreach ($itemcodeArr as $item) 
			get_status_israpost($item);
	}

//	system("/usr/bin/mailx -r alexansh@amdocs.com -s \"Log\" $email, alexansh@amdocs.com < $logFile");
//The item code typed is invalid or misstyped, it cannot be recognized by the system.
//The postal item arrived at the Neve Noy postal unit in Beer Sheva. The addressee was notified on 18/04/2013 to claim the item at the postal unit.
//No information is available for the item RQ156150817SD.
	?>
	</body>
</html>
