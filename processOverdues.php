<?php
## Copyright 2006 Kyle Hall

## Modified a bunch by 2008 Darrell Ulm

## This file is part of koha-tools.

## koha-tools is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## koha-tools is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with koha-tools; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

/**
  * This report generates lists of overdue items to use for shelfchecking
  * and pdf's of overdue notices to be printed out and mailed to the borrower.
  * This file displays the list of overdues or the pdf of notices.
  * Note: This one is a bit messy and could use cleaning up, switch to templates.
  * @package koha-tools
  * @subpackage koha-reports
  * @author Kyle Hall
  * @copyright 2006
  */

ini_set("memory_limit","256M");
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");

// Get the PDF stuff - I didn't modify this
require_once('fpdf/fpdf.php');

// Modified this a bit, this is the output pdf file - d.u.
require_once('ReportPdf.class.php');

$library = $_POST['library'];
$patrons = $_POST['patrons'];
$noticeNumber = $_POST['noticeNumber'];
$message = $_POST['message'];
$lname = $_POST['lname'];
$fname = $_POST['fname'];
$holidays = $_POST['holidays'];
$itypelist = $_POST['itypelist'];
$specialdaysago = $_POST['specialdaysago'];
$daysAgo1 = $_POST['daysAgo1'];
$daysAgo2 = $_POST['daysAgo2'];
$reporttype = $_POST['reportType'];
$email = $_POST['email'];

if ($reporttype == "shelf")
	{
	// Print the main top menu
	require_once("topmenu.php");
	echo "Library: $library <br>";	
	echo "noticeNumber $noticeNumber <br>";	
	echo "lname $lname <br>";
	echo "fname $fname <br>";		
	echo "holidays $holidays <br>";
	echo "itypelist $itypelist <br>";
	echo "specialdays ago $specialdaysago <br>";
	echo "daysAgo1 $daysAgo1 <br>";
	echo "daysAgo2 $daysAgo2 <br>";
	echo "reporttype $reporttype <br><br>";
	}

$sendout 		= 0;
//comand line override!
if (sizeof($argv)>1)
	{
	echo "running on command line<br>";
	$sendout 		= 1;
	$library		= $argv[1];
	$noticeNumber	= $argv[2];
	$message		= $argv[3];
	$lname			= $argv[4];
	$fname			= $argv[5];
	$holidays		= $argv[6]; 
	$itypelist		= $argv[7];
	$specialdaysago	= $argv[8];
	$daysAgo1		= $argv[9];
	$daysAgo2		= $argv[10];
	$reporttype		= $argv[11];
	$bortypes		= $argv[12];
	print_r($argv);
	$selectedTypes = explode(" ",$bortypes);
	print_r($selectedTypes);
	$patrons = $selectedTypes;
	}

$itypearray = explode(" ", $itypelist );

// Check for holidays
$sweek = $week;
$holidays = get_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//Get the data about the library from the database.
$sql = 'SELECT * FROM branches WHERE branches.branchcode = "' . $library . '"';
// Perform Query
$resultSet = dbquery($conn, $sql);
if ($num_rows = numrows($resultSet) == 0) die("What branch?");

$row = read_db_assoc($resultSet);
$row = stripslashes_deep($row);

$libraryData = array();
$libraryData['name'] = $row['branchname'];
$libraryData['address1'] = $row['branchaddress1'];
$libraryData['address2'] = $row['branchaddress2'];
$libraryData['address2'] = $row['branchaddress2'];
$libraryData['phone'] = $row['branchphone'];
$libraryData['fax'] = $row['branchfax'];
$libraryData['email'] = $row['branchemail'];

if ($reporttype == "email") $holidays = 0;    		// Send emails based on exact day, not holidays

// Set the order correctly
if($daysAgo1 > $daysAgo2){
	$daysAgo[0] = $daysAgo1 + $holidays;
	$daysAgo[1] = $daysAgo2;
} else {
	$daysAgo[0] = $daysAgo2 + $holidays;
	$daysAgo[1] = $daysAgo1;
}

//Check to make sure all neccessary data exists
$anyErrors = false;
if (count($patrons) < 1){
	echo "You must choose at least one Patron Type<br>";
	$anyErrors = true;
}
if (count($daysAgo) < 2 ) {
	echo "You must fill in both 'Days Ago' boxes<br>";
	$anyErrors = true;
}
if($anyErrors){
	echo "Please use your browser's back button to return to the last page and fill in the missing data.<br>";
	echo "<br>Thank you. - Management<br>";
	die();
}

$sql = 'SELECT *
		FROM issues, borrowers, items, biblio 
		WHERE 
		(
		issues.return IS NULL 
		AND borrowers.borrowernumber = issues.borrowernumber AND borrowers.surname LIKE "'.$lname.'%"
		AND borrowers.firstname LIKE "'.$fname.'%"
		AND items.itemnumber = issues.itemnumber
		AND biblio.biblionumber = items.biblionumber
		AND returndate IS NULL  
		AND
		(
			( 
				issues.date_due >= DATE_SUB(CURDATE(), INTERVAL '.$daysAgo[0].' DAY)
			AND
				issues.date_due <= DATE_SUB(CURDATE(), INTERVAL '.$daysAgo[1].' DAY)
			) 
		OR
			(
			issues.date_due = DATE_SUB(CURDATE(), INTERVAL '.$specialdaysago.' DAY) ';
			$sql .= build_items($itypearray);
			$sql .= ')
		)
	) ';
	$sql .= build_patron_categories($patrons);
	$sql .= ' ORDER BY borrowers.borrowernumber';

//echo "$sql <br>";

// Perform Query
$resultSet = dbquery($conn, $sql);
if ($num_rows = numrows($resultSet) == 0) die("No Overdues");

$reportLines = array();
$i = 0;
while ($row = read_db_assoc($resultSet))
	{
	$row = stripslashes_deep($row);
	$reportLines[$i] = array();
	$reportLines[$i]['emailaddress'] = $row['email'];
	$reportLines[$i]['borrowernumber'] = $row['borrowernumber'];
	$reportLines[$i]['fname'] = $row['firstname'];
	$reportLines[$i]['lname'] = $row['surname'];
	$reportLines[$i]['barcode'] = $row['barcode'];
	$reportLines[$i]['title'] = $row['title'];
	$reportLines[$i]['author'] = $row['author'];
	$reportLines[$i]['itemcallnumber'] = $row['itemcallnumber'];
	$reportLines[$i]['price'] = $row['replacementprice'];
	$reportLines[$i]['timestamp'] = date('Y-m-d', mysqlToUnixTimestamp($row['timestamp']));
	$reportLines[$i]['date_due'] = $row['date_due'];
	$reportLines[$i]['itype'] = $row['itype'];
	$reportLines[$i]['location'] = $row['location'];
	$reportLines[$i]['address'] = $row['address'];
	$i++;
	}

switch($reporttype){
	case "shelf":
		getShelfReport($reportLines);
		break;
	case "email":
		getEmailReport($reportLines, $message);
		break;
	case "mail":
		getMailNotices($conn, $libraryData, $noticeNumber, $reportLines, $message, $email);
		break;
}

function getEmailReport($reportLines, $message){
	$lastemail = "!@#$%^&*";
	$emailstr = "";
	$body = "";
	echo "<br>Number of Overdues:".count($reportLines)."<br><br>";
	foreach($reportLines as $row)
		{
		$newemail = $row['emailaddress'];
		$emailvalid = 0;
		if (Verify_Email_Address($newemail))	$emailvalid = 1;
		if ($emailvalid)
			{
			if ($lastemail != $newemail && $lastemail != "!@#$%^&*")
				{
				echo $sendemail ." | ".$sendemail." | ".$emailstr."\n\n";
				send_the_email($sendemail,$subject, $emailstr);
				}
/*				$to = "testemail@foo.foo";
				$subject = $sendemail;
				$body = $emailstr;
				if (mail($to, $subject, $body))
					  echo("<p>Message successfully sent!</p>");
					   else 
					  echo("<p>Message delivery failed...</p>");
*/					  
		// RESET THE EMAIL MESSAGE BELOW
			if ($lastemail != $newemail)	
				{
				$emailstr = "";
				$emailstr .= "From: \n Stow-Munroe Falls Public Library \n 3512 Darrow Rd. \n Stow, OH 44224 \n Ph:330-688-3295\n\n";
				$emailstr .= "\n";
				$emailstr .=  "This is ".$message." notice for: ".$row['fname'] . " ". $row['lname'] ."\n";
				$subject = "This is ".$message." notice for: ".$row['fname'] . " ". $row['lname'] ."\n";
				$emailstr .=  "at e-mail address: ".$row['emailaddress'] . "\n\n";
				$emailstr .=  "Please login to http://opac.smfpl.org to renew and check your account\n\n";
				$emailstr .=  "If the following items are not returned by the due date, fines may be assessed.\n\n";
				$emailstr .=  "Thank you.\n\n";
				
				$sendemail = $row['emailaddress'];
				//$sendemail = 'darrellulm@smfpl.org';
				}
			$emailstr .=  "Title: " . $row['title']."\n";
			if ($row['author'] != NULL) $emailstr .=  "Author: " . $row['author'] ."\n";
			$emailstr .=  "Call Number: " . $row['itemcallnumber']."\n";
			$emailstr .=  "Format: " . $row['itype'] ."\n";
			$emailstr .=  "Barcode: " . $row['barcode']."\n";
			$emailstr .=  "Date Due: (" . $row['date_due'] .")\n";
			$emailstr .=  "\n";
			}
		$lastemail = $newemail;
		}
if ($lastemail != "!@#$%^&*") send_the_email($sendemail,$subject, $emailstr);
}

function send_the_email($to, $subject, $body)
{
if ($body != "")
	if (mail($to, $subject, $body))
	  	echo("<p>Message successfully sent!</p>");
	   		else 
	  	echo("<p>Message delivery failed...</p>");
}


function Verify_Email_Address($email_address)
         {
         //Assumes that valid email addresses consist of user_name@domain.tld
         $at = strpos($email_address, "@");
         $dot = strrpos($email_address, ".");

         if($at === false ||
            $dot === false ||
            $dot <= $at + 1 ||
            $dot == 0 ||
            $dot == strlen($email_address) - 1)
            return(false);

         $user_name = substr($email_address, 0, $at);
         $domain_name = substr($email_address, $at + 1, strlen($email_address));

         if(Validate_String($user_name) === false ||
            Validate_String($domain_name) === false)
            return(false);

         return(true);
         }

function Validate_String($string)
         {
         $valid_chars = "1234567890-_.^~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $invalid_chars = "";

         if($string == null || $string == "")
            return(true);

         //For every character on the string.
         for($index = 0; $index < strlen($string); $index++)
            {
            $char = substr($string, $index, 1);

            //Is it a valid character?
            if(strpos($valid_chars, $char) === false)
              {
              //If not, is it already on the list of invalid characters?
              if(strpos($invalid_chars, $char) === false)
                {
                //If it's not, add it.
                if($invalid_chars == "")
                   $invalid_chars .= $char;
                else
                   $invalid_chars .= ", " . $char;
                }
              }
            }

         //If the string does not contain invalid characters, the function will return true.
         //If it does, it will either return false or a list of the invalid characters used
         //in the string, depending on the value of the second parameter.
         if($return_invalid_chars == true && $invalid_chars != "")
           {
           $last_comma = strrpos($invalid_chars, ",");

           if($last_comma != false)
              $invalid_chars = substr($invalid_chars, 0, $last_comma) .
              " and " . substr($invalid_chars, $last_comma + 1, strlen($invalid_chars));

           return($invalid_chars);
           }
         else
           return($invalid_chars == "");
         }

function getShelfReport($reportLines){
	echo '<table style="text-align: left; width: 100%;" border="0"
		cellpadding="2" cellspacing="2">
		<tr bgcolor="lightgray">
			<th>Email</th>
			<th>First</th>
			<th>Last</th>
			<th>Address</th>
			<th>Callnumber</th>
			<th>IType</th>
			<th>Title</th>
			<th>Author</th>
			<th>Barcode</th>
			<th>Date Due</th>
			<th>Borrower Number</th>
		</tr>
		';
	$color = 'white';

	echo "<br>Number of Overdues:".count($reportLines)."<br>";
	foreach($reportLines as $row){
		echo "	<tr bgcolor='" . $color . "'>
			<td>" . $row['emailaddress'] . "</td>
			<td>" . $row['fname'] . "</td>
			<td>" . $row['lname'] . "</td>
			<td>" . $row['address'] . "</td>
			<td>" . $row['itemcallnumber'] . "</td>
			<td>" . $row['itype']. "</td>
			<td>" . $row['title'] . "</td>
			<td>" . $row['author'] . "</td>
			<td>" . $row['barcode'] . "</td>
			<td>" . $row['date_due'] . "</td>
			<td>" . $row['borrowernumber'] . "</td>
			</tr>";
	if ($color == 'white') {$color = 'lightgray';} else {$color = 'white';}
	}
}

function getMailNotices($conn, $libraryData, $noticeNumber, $reportLines, $message, $email){
	$noticeData = hashDataByBorrowernumber($conn, $reportLines, $email);
	$pdf = new ReportPdf();
	$pdf->createNotices($libraryData, $noticeNumber, $noticeData, $message);
	$pdf->Output();
}

function hashDataByBorrowernumber($conn, $reportLines, $email){
	$currentBorrower = null;
	$hashedArray = array();
	foreach($reportLines as $thisLine)
		{
		if ($thisLine['emailaddress'] == "" || $email)
			{
			if ($currentBorrower == $thisLine['borrowernumber']){
				$hashedArray[$currentBorrower]['itemsDue'][] = $thisLine;
			} else {
				$currentBorrower = $thisLine['borrowernumber'];
				$hashedArray[$currentBorrower] = array();
				$hashedArray[$currentBorrower]['itemsDue'] = array();			
				$hashedArray[$currentBorrower]['itemsDue'][] = $thisLine;
				
				// Perform Query
				$sql = "select * from borrowers where borrowernumber = ".$thisLine['borrowernumber'];
				$resultSet = dbquery($conn, $sql);
				
				$row = read_db_assoc($resultSet);
				$row = stripslashes_deep($row);
				
				$thisBorrower = array();
				$thisBorrower['name'] = $row['surname'].", ".$row['firstname'];
				$thisBorrower['street'] = $row['address'];
				$thisBorrower['cityStateZip'] = $row['city']." ".$row['zipcode'];
				$hashedArray[$currentBorrower]['borrower'] = $thisBorrower;
			}
		}
	}
	return $hashedArray;
}

function mysqlToUnixTimestamp($mysql_timestamp){
	preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/',$mysql_timestamp,$pieces);
	$unix_timestamp = mktime($pieces[4], $pieces[5], $pieces[6],$pieces[2], $pieces[3], $pieces[1]);
	return($unix_timestamp);
}
?>
