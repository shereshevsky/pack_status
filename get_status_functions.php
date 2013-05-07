<?php

function fn_israpost($itemcode, $email, $periodic) {
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
	$html->clear();
	unset($html);

	if (strrpos($txt, "There is no information") <> 0 || strrpos($txt, "No information is available") <> 0) {
		if ($email)
			fn_save_mail($itemcode, $email);
		return "$itemcode: There is no information regarding the package, your email was added to notification list";
	}elseif (strrpos($txt, "ברקוד לא חוקי") <> 0 || strrpos($txt, "The item code typed is invalid or misstyped") <> 0) {
		return "$itemcode: Invalid Tracking number.";
	}elseif (strrpos($txt, "The postal item was delivered") <> 0 || strrpos($txt, "The postal item arrived") <> 0) {
		if ($periodic) {
			fn_send_mail($itemcode, $email, $txt);
			fn_delete_request($itemcode, $email);
		}
		else 
			return "$itemcode: ".substr($txt, strpos($txt, "</h3>"));
	}else{
		return $txt;
	}
}

function fn_save_mail($itemcode, $email) {
	global $db;
	$insertData = array(
		'email' => $email,
		'tr_number' => strtoupper($itemcode)
	);
	$db->Replace($insertData, 'requests');
}

function fn_send_mail($itemcode, $email, $txt) {
	$headers = "From: a‫dmin@kelim2go.com\r\n";
	$headers .= "Reply-To: ‫admin@kelim2go.com\r\n";
	$headers .= "X-Mailer: PHP/".phpversion();
	mail($email, 'Package '.$itemcode.' status was changed', substr(substr($txt, strpos($txt, "</h3>")+5),0,-4)."\r\n http://www.kelim2go.com/pack_status/", $headers);
}

function fn_periodic_check() {
	global $db;
	$results = $db->ExecuteSQL('SELECT tr_number, email  FROM requests');
	foreach ($results as $request) {
		if (is_array($request))
			fn_israpost($request['tr_number'], $request['email'], true);
	}
}

function fn_delete_request($itemcode, $email) {
	global $db;
	$db->ExecuteSQL("INSERT INTO requests_his (added, email, tr_number) 
					SELECT added, email, tr_number 
					FROM requests WHERE email = '$email' AND tr_number = '$itemcode'"); 
	$db->ExecuteSQL("DELETE FROM requests WHERE email = '$email' AND tr_number = '$itemcode'"); 
}
?>