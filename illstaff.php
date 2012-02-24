<?php
$data['email_to'] = $_POST['Supervisor'];

if ( empty($_POST['E-mail']) ) :
	$data['email_from'] = "stowref@oplin.org";
else:
	$data['email_from'] = $_POST['staffemail'];
endif;

$data['headers'] = 'From: ' . $data['email_from'] . "\r\n";
$data['headers'] .= 'MIME-Version: 1.0' . "\r\n";
$data['headers'] .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$data['body'] .= "<p>Date submitted:  " . date("F j, Y, g:i a") . "</p>";  
echo $data['body'];

foreach($_POST as $row=>$value) :
$data['body'] .= "<p><strong>$row:</strong> $value</p>";
echo "<p><strong>$row:</strong> $value</p>";
endforeach;



mail($data['email_to'],'Interlibrary Loan Request Form',$data['body'],$data['headers']);
?>
<html>
<head>
<!--<meta http-equiv="refresh" content="4;URL=http://www.smfpl.org/illstaff.htm">
-->
<title>Your request has been sent</title></head>
<body>
Your request has been sent. <a href="http://66.213.124.205:443/cgi-bin/koha/reports-home.pl">Click here to return to the reports page</a>
</body>
</html>