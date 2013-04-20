<?php

function get_status_israpost($itemcode) {

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

	foreach($html->find('div#itemcodeinfoPrt') as $e)
		echo $e->innertext . '<br>';

	$html->clear();
	unset($html);
}

?>
