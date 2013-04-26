<?php

function fn_israpost($itemcode, $email) {

	$context = array
	(
	       'http' => array
	       (
	        //      'proxy' => 'genproxy.amdocs.com:8080',
	        //      'request_fulluri' => true, 
	       ),
	);

	$context = stream_context_create($context); 
	 
	$html= file_get_html("http://www.israelpost.co.il/itemtrace.nsf/mainsearch?OpenForm&L=EN&itemcode=$itemcode", false, $context);

	$txt = "";
	foreach($html->find('div#itemcodeinfoPrt') as $e)
		$txt .= $e->innertext . '<br>';

	if (strrpos($txt, "There is no information") <> 0 || strrpos($txt, "No information is available") <> 0) {
		echo "There is no information regarding the package $itemcode, your email was added to notification list";
		fn_save_mail($itemcode, $email);
	} elseif (strrpos($txt, "The postal item was delivered") <> 0)
		echo $txt;
	else
		echo $txt;
	$html->clear();
	unset($html);
}

function fn_save_mail($itemcode, $email) {
	global $db;
	$insertData = array(
		'email' => '$email',
		'itemcode' => '$itemcode'
	);

	$db->insert('requests', $insertData);
	//print_r($results);
}

?>