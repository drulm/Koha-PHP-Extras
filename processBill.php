<?php
## Koha Billing Processing

## Modified by Darrell Ulm 2008

## Built from:
## KOHA OVERDUES

## Copyright 2006 Kyle Hall

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
  * This file displays a form to get the settings for the overdues.
  * Note: This one is a bit messy and could use cleaning up, switch to templates.
  * @package koha-tools
  * @subpackage koha-reports
  * @author Kyle Hall
  * @copyright 2006
  */

// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
ini_set("memory_limit","256M");

// Set up the PDF Stuff
require_once('fpdf/fpdf.php');
require_once('BillPdf.class.php');

$library = $_POST['library'];
$patrons = $_POST['patrons'];
$noticeNumber = $_POST['noticeNumber'];
$message = $_POST['message'];
$lname = $_POST['lname'];
$fname = $_POST['fname'];
$dollarcutoff = $_POST['dollarcutoff'];
$holidays = $_POST['holidays'];
$itypelist = $_POST['itypelist'];
$specialdaysago = $_POST['specialdaysago'];

$itypearray = explode(" ", $itypelist );

if ($reporttype == "shelf")
	{
	// Print the main top menu
	require_once("topmenu.php");
	}

// Check for holidays
$sweek = $week;
$holidays = get_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//Get the data about the library from the database.
$sql = 'SELECT * FROM branches WHERE branches.branchcode = \'' . $library . '\'';

// Perform Query
$resultSet = dbquery($conn, $sql);
if ($num_rows = numrows($resultSet) == 0) die("What branch?");

$row = read_db_assoc($resultSet);
$row = stripslashes_deep($row);

//$row = mysql_fetch_array($resultSet);
$libraryData = array();
$libraryData['name'] = $row['branchname'];
$libraryData['address1'] = $row['branchaddress1'];
$libraryData['address2'] = $row['branchaddress2'];
$libraryData['address3'] = $row['branchaddress3'];
$libraryData['phone'] = $row['branchphone'];
$libraryData['fax'] = $row['branchfax'];
$libraryData['email'] = $row['branchemail'];

if($_POST['daysAgo1'] > $_POST['daysAgo2']){
	$daysAgo[0] = $_POST['daysAgo1'] + $holidays;
	$daysAgo[1] = $_POST['daysAgo2'];
} else {
	$daysAgo[0] = $_POST['daysAgo2'] + $holidays;
	$daysAgo[1] = $_POST['daysAgo1'];
}

//Check to make sure all neccessary data exists
$anyErrors = false;
if (count($patrons) < 1 ) {
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

switch($_POST['reportType']){
	case "shelf":
		$orderBy = 'itemcallnumber';
		break;
	case "mail":
		$orderBy = 'borrowers.borrowernumber';
		break;
}

$sql = 'SELECT 
	#	GROUP_CONCAT(accounttype ORDER BY accountlines.date DESC SEPARATOR " <br> "),
		GROUP_CONCAT(description ORDER BY accountlines.date DESC SEPARATOR " <br> ") ldesc,
		GROUP_CONCAT(amountoutstanding ORDER BY accountlines.date DESC SEPARATOR " <br> ") as lamount, 
		GROUP_CONCAT(accountlines.date ORDER BY accountlines.date DESC SEPARATOR " <br> ") as ldate, 
	#	GROUP_CONCAT(itemnumber ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
	#	count(*), 
		max(accountlines.date),
		sum(amountoutstanding), 
		borrowers.borrowernumber, borrowers.surname, borrowers.firstname, 
		itemnumber
	#	description, 
	#	amountoutstanding, 
	#	accountlines.date
		FROM borrowers, accountlines
		WHERE 
		accountlines.borrowernumber = borrowers.borrowernumber';
		if ($lname <> "%") $sql .= ' AND borrowers.surname = "'. $lname . '" ';
		if ($fname <> "%") $sql .= ' AND borrowers.firstname = "' . $fname . '" '; 
		$sql .= ' AND borrowers.branchcode = "' . $library . '" ';
		$sql .= build_patron_categories($patrons);
		$sql .= 'GROUP BY accountlines.borrowernumber
		HAVING sum(amountoutstanding) >= ' . $dollarcutoff;

// Perform Query
$resultSet = dbquery($conn, $sql);
if ($num_rows = numrows($resultSet) == 0) die("No Bills");

$today = strtotime("now"); 
$daysago0 = $today - $daysAgo[0] * 86400;
$daysago1 = $today - $daysAgo[1] * 86400;

//do some sort of selection calculation here or sum by date...
// Gross logic warning!!! Woop woop!
$reportLines = array();
$i = 0;
while ($row = read_db_assoc($resultSet))
	{
	$row = stripslashes_deep($row);
//while($row = mysql_fetch_array($resultSet))
//	{
	$date_flag = 0;
	if ($row['sum(amountoutstanding)'] > $dollarcutoff)
		{
//		$datearray = explode("<br>", $row['GROUP_CONCAT(accountlines.date ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$datearray = explode("<br>", $row['ldate']);
//		$feearray = explode("<br>", $row['GROUP_CONCAT(amountoutstanding ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$feearray = explode("<br>", $row['lamount']);
//		$descarray = explode("<br>", $row['GROUP_CONCAT(description ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$descarray = explode("<br>", $row['ldesc']);
		$prefines = 0.0; 						// calculate for date range
		$postfines = 0.0; 
		$allfines = 0.0;
		$payments = 0.0;
		$ti = 0;
		foreach($datearray as $charge_date)
			{
			//find start date in description when the charges BEGAN 
			$todays_date = date("Y-m-d"); 
			$stdatetrim = rtrim($descarray[$ti]);
			$startdate = substr($stdatetrim,strlen($stdatetrim)-10,10);
			$timest = false;
			if ($startdate[2]=='/' && $startdate[5]=='/') 
				{
				$timest = strtotime($startdate);
				$timststr = date('Y-m-d', $timest);
				}
			$unix_cd = strtotime($charge_date);
			if ($timest != false) $unix_cd = $timest;
			$chd = date("Ymd", $unix_cd);
			$da0 = date("Ymd", $daysago0);
			$da1 = date("Ymd", $daysago1);
			if ($chd < $da0) 
				$prefines += $feearray[$ti];
			if ($chd <= $da1) 
				$postfines += $feearray[$ti];
			else
				if ($feearray[$ti] < 0) 
					{
					$postfines += $feearray[$ti];
					$payments += $feearray[$ti];
					}
			$allfines += $feearray[$ti];
			if ($chd >= $da0 && $chd <= $da1)
				$date_flag=1;
			$ti++;
			}
		if ($prefines >= $dollarcutoff || $postfines <= $dollarcutoff) // dont send a bill, we already sent one!!!
			$date_flag=0;
		}
	
	if ($date_flag == 1) 
		{
		$reportLines[$i] = array();
		$reportLines[$i]['borrowernumber'] = $row['borrowernumber'];
		$reportLines[$i]['payments'] = $payments;
		
		switch($_POST['reportType']){
			case "shelf":
				$reportLines[$i]['chargecount'] = $row['itemnumber'];
				$reportLines[$i]['prefines'] = $prefines;
				$reportLines[$i]['postfines'] = $postfines;
				$reportLines[$i]['allfines'] = $allfines;
				$reportLines[$i]['surname'] = $row['surname'];
				$reportLines[$i]['firstname'] = $row['firstname'];
				$reportLines[$i]['amountoutstanding'] = $row['sum(amountoutstanding)'];
				$reportLines[$i]['da0'] = $da0;
				$reportLines[$i]['da1'] = $da1;
				break;
			case "mail":
				$reportLines[$i]['itype'] = $row['firstname'];
				break;
				}
		
		$sql2 = "SELECT 
				description, 
				date, 
				accountlines.itemnumber as inum, 
				accounttype, 
				borrowers.borrowernumber, 
				dispute, 
		#		barcode,
				amountoutstanding
		#		min(amountoutstanding)
			FROM borrowers, accountlines
		#	, items
			WHERE 
				borrowers.borrowernumber = '" . $row['borrowernumber'] ."'
					and
				accountlines.borrowernumber = borrowers.borrowernumber
		#			and		
		#		accountlines.itemnumber = items.itemnumber

		#	GROUP by accountlines.timestamp
		#	GROUP by itemnumber
			ORDER by date";
		
		$reportLines[$i]['description'] = '';
		$reportLines[$i]['dispute'] = '';
		$reportLines[$i]['feeitem'] = '';
		$reportLines[$i]['barcode'] = '';
		$reportLines[$i]['author'] = '';
		$reportLines[$i]['title'] = '';
		$reportLines[$i]['itemcallnumber'] = '';
		$reportLines[$i]['price'] = '';
		$reportLines[$i]['timestamp'] = '';
		$reportLines[$i]['date_due'] = '';
		$reportLines[$i]['realbarcode'] = '';
		
		// Perform Query
		$resultSet2 = dbquery($conn, $sql2);
//		echo "<br> $sql2 <br>";
		while ($row2 = read_db_assoc($resultSet2))
			{
			$row2 = stripslashes_deep($row2);
			$sql3 = "SELECT barcode from items where
				items.itemnumber = '" . $row2['inum'] ."' ";
			$resultSet3 = dbquery($conn, $sql3);
			$row3 = read_db_assoc($resultSet3);
			$barc = substr(stripslashes($row3['barcode']),7,7);
			
			if ($row2['amountoutstanding'] != 0.0)
				switch($_POST['reportType'])
					{
					case "shelf":
						$reportLines[$i]['description'] .= $row2['description']. " <br> ";
						//$reportLines[$i]['feeitem'] .= $row2['min(amountoutstanding)'] ." <br> ";
						$reportLines[$i]['feeitem'] .= $row2['amountoutstanding'] ." <br> ";
						$reportLines[$i]['dispute'] .= $row2['dispute']. " <br> ";
						$reportLines[$i]['barcode'] .= $row2['date']. " <br> ";
						break;
					case "mail":
						//$reportLines[$i]['author'] .= $row2['barcode']. " <br> ";
						$reportLines[$i]['author'] .=  substr($row3['barcode'],7,7) . " <br> ";
						$reportLines[$i]['title'] .= $row2['description']. " <br> ";
						$reportLines[$i]['itemcallnumber'] .= $row2['accounttype']. " <br> ";
						$reportLines[$i]['dispute'] .= $row2['dispute']. " <br> ";
						//$reportLines[$i]['price'] .= $row2['min(amountoutstanding)']. " <br> ";
						$reportLines[$i]['price'] .= $row2['amountoutstanding']. " <br> ";
						//$reportLines[$i]['timestamp'] .= $row2['min(amountoutstanding)']. " <br> ";
						$reportLines[$i]['timestamp'] .= $row2['amountoutstanding']. " <br> ";
						$reportLines[$i]['date_due'] .= $row2['date']. " <br> ";		
						break;
					}
			}
		$i++;
		}
	}
	
switch($_POST['reportType'])
	{
	case "shelf":
		getShelfReport($reportLines,$dollarcutoff);
		break;
	case "mail":
		getMailNotices($conn, $libraryData, $noticeNumber, $reportLines, $message);
		break;
	}

//-------------------------------------- Functions --------------------------------------------------
function getShelfReport($reportLines,$dollarcutoff){
	echo '<table style="text-align: left; width: 100%;" border="0"
		cellpadding="2" cellspacing="2">
		<tr bgcolor="lightgray">
			<td><strong>Charges</strong></td>		
			<td>Borrower Number</td>
			<td><strong>Last Name</strong></td>
			<td><strong>First Name</strong></td>
			<td><strong>Fee Item</strong></td>
			<td><strong>Description</strong></td>
			<td><strong>Dispute</strong></td>
			<td><strong>Amount Outstanding</strong></td>
			<td><strong>Date</strong></td>
			<td><strong>Barcode</strong></td>
			<td>Prefines</td>
		</tr>
		';
	$color = 'white';
	echo "<br>Total Number of Charges for all Patrons ".count($reportLines)."<br>";

	$total = 0.0;
	foreach($reportLines as $row)
		{
		if ( $row[amountoutstanding] >= $dollarcutoff )
			{
			echo "	<tr bgcolor='" . $color . "'>
				<td>" . $row['chargecount'] . "</td>
				<td>" . $row['borrowernumber'] . "</td>
				<td>" . $row['surname'] . "</td>
				<td>" . $row['firstname'] . "</td>
				<td>"; 
			printf("%s",$row['feeitem']);
			echo "</td>	
				<td>" . $row['description']. "</td>
				<td>" . $row['dispute'] . "</td>
				<td>"; 
			printf("%10.2f",$row['amountoutstanding']);
			echo "</td>
				<td>" . $row['date'] . "</td>
				<td>" . $row['barcode'] . "</td>
				<td>pre: $" . $row['prefines'] . "<br>post: $". $row['postfines'] . "<br>all: $". $row['allfines'] ."<br> da0:" . $row['da0'] ."<br> da1:". $row['da1'] . "</td>
				</tr>";
			if ($color == 'white') {$color = 'lightgray';} else {$color = 'white';}
			$total += $row['amountoutstanding'];
			}
		}
		printf("<br> <b> TOTAL FOR ALL PATRONS FOR THE BILL CUTOFF DATE = %10.2f </b>",$total);
}

function getMailNotices($conn, $libraryData, $noticeNumber, $reportLines, $message){
	$noticeData = hashDataByBorrowernumber($conn, $reportLines);
	$pdf = new ReportPdf();
	$pdf->createNotices($libraryData, $noticeNumber, $noticeData, $message);
	$pdf->Output();
}

function hashDataByBorrowernumber($conn, $reportLines){
	$currentBorrower = null;
	$hashedArray = array();
	foreach($reportLines as $thisLine){
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
	return $hashedArray;
}

function getCategoryCodesSql($selectedTypes){
	$sql = "(categorycode = '" . $selectedTypes[0] . "'";
	for($i = 1; $i < count($selectedTypes); $i++){
		$sql .= " OR categorycode = '" . $selectedTypes[$i] . "'";
	}
	$sql .= ")";
	return $sql;
}

function mysqlToUnixTimestamp($mysql_timestamp){
	preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/',$mysql_timestamp,$pieces);
	$unix_timestamp = mktime($pieces[4], $pieces[5], $pieces[6],$pieces[2], $pieces[3], $pieces[1]);
	return($unix_timestamp);
}
?>

