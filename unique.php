<?php
## Koha Unique Management Process

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

ini_set("memory_limit","256M");
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
// Print the main top menu
require_once("topmenu.php");

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
$daysago1 = $_POST['daysAgo1'];
$daysago2 = $_POST['daysAgo2'];
$reporttype = $_POST['reportType'];

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
	$dollarcutoff	= $argv[6];
	$holidays		= $argv[7]; 
	$itypelist		= $argv[8];
	$specialdaysago	= $argv[9];
	$filesave		= $argv[10];
	$daysago1 		= $argv[11];
	$daysago2 		= $argv[12];
	$reporttype 	= $argv[13];
	$bortypes		= $argv[14];
	print_r($argv);
	$selectedTypes = explode(" ",$bortypes);
	print_r($selectedTypes);
	$patrons = $selectedTypes;
	$sendout = 1;
	}
	
echo "<center>Copy and paste the result into a secure email, or use the script to create a file and SFTP, do not use FTP!</center><br><br>";

$itypearray = explode(" ", $itypelist );

// Check for holidays
$sweek = $week;
$holidays = get_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

if($daysago1 > $daysago2){
	$daysAgo[0] = $daysago1 + $holidays;
	$daysAgo[1] = $daysago2;
} else {
	$daysAgo[0] = $daysago2 + $holidays;
	$daysAgo[1] = $daysago1;
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


$sql = 'SELECT 
		#	GROUP_CONCAT(accounttype ORDER BY accountlines.date DESC SEPARATOR " <br> "),
			GROUP_CONCAT(description ORDER BY accountlines.date DESC SEPARATOR " <br> "),
			GROUP_CONCAT(amountoutstanding ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
			GROUP_CONCAT(accountlines.date ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		#	GROUP_CONCAT(itemnumber ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		#	GROUP_CONCAT(accounttype ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		#	count(*), 
			max(accountlines.date), 
			min(accountlines.date), 
			sum(amountoutstanding), 
			borrowers.borrowernumber, borrowers.surname, borrowers.firstname, borrowers.dateofbirth,
			borrowers.city, borrowers.phone, borrowers.zipcode,
			borrowers.cardnumber, borrowers.categorycode, borrowers.sort1, borrowers.contactname, borrowers.address,
			itemnumber,description, amountoutstanding, accountlines.date
		FROM borrowers, accountlines
			WHERE 
		accountlines.borrowernumber = borrowers.borrowernumber
		';
		if ($lname <> "%") $sql .= ' AND borrowers.surname LIKE "'. $lname . '" ';
		if ($fname <> "%") $sql .= ' AND borrowers.firstname LIKE "' . $fname . '" ';
		$sql .= build_patron_categories($patrons);
		$sql .= ' GROUP BY accountlines.borrowernumber HAVING sum(amountoutstanding) >= ' . $dollarcutoff ;

//echo "<hr>".$sql."<hr>";

// Perform Query
$resultSet = dbquery($conn, $sql);
if ($num_rows = numrows($resultSet) == 0) die("No Collections");

$today = strtotime("now"); 
$daysago0 = $today - $daysAgo[0] * 86400;
$daysago1 = $today - $daysAgo[1] * 86400;

//do some sort of selection calculation here or sum by date...
$reportLines = array();
$i = 0;
while ($row = read_db_assoc($resultSet))
	{
	$row = stripslashes_deep($row);
	
	$date_flag = 0;
	if ($row['sum(amountoutstanding)'] >= $dollarcutoff)
		{
		$datearray = explode("<br>", $row['GROUP_CONCAT(accountlines.date ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$feearray = explode("<br>", $row['GROUP_CONCAT(amountoutstanding ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$descarray = explode("<br>", $row['GROUP_CONCAT(description ORDER BY accountlines.date DESC SEPARATOR " <br> ")']);
		$prefines = 0.0; 						// calculate for date range
		$postfines = 0.0; 
		$allfines = 0.0;
		$ti = 0;
		$uniquefee = '<b><i>No<br>Add $10 Fee</i></b>';
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
			if ($feearray[$ti] == 10.00) $uniquefee = 'Yes';
			if ($debug) if (! $sendout) echo "$chd $da0 $da1 ".$feearray[$ti]."\n<br>";
			if ($chd < $da0) 
				$prefines += $feearray[$ti];
			if ($chd <= $da1) 
				$postfines += $feearray[$ti];
			else
				if ($feearray[$ti] < 0) $postfines += $feearray[$ti];
			$allfines += $feearray[$ti];
			if ($chd >= $da0 && $chd <= $da1)
				{
				$date_flag=1;
				}
			$ti++;
			}
		if (! $sendout && $debug)
			echo "name: ".$row['surname'].",".$row['firstname']." cardnumber:".$row['cardnumber']." pre:$prefines  post:$postfines   allfines:$allfines\n<br>";
		if ($dollarcutoff > 0 && ($prefines >= $dollarcutoff || $postfines <= $dollarcutoff)) // dont send a bill, we already sent one!!!
			$date_flag=0;
		}
	
	if ($date_flag == 1) 
		{
		$reportLines[$i] = array();
		$reportLines[$i]['borrowernumber'] = $row['borrowernumber'];
		$reportLines[$i]['unique'] = $uniquefee;
		$reportLines[$i]['chargecount'] = $row['itemnumber'];
		$reportLines[$i]['prefines'] = $prefines;
		$reportLines[$i]['postfines'] = $postfines;
		$reportLines[$i]['allfines'] = $allfines;
		$reportLines[$i]['surname'] = $row['surname'];
		$reportLines[$i]['firstname'] = $row['firstname'];
		$reportLines[$i]['streetaddress'] = $row['address'];
		$reportLines[$i]['city'] = $row['city'];
		$reportLines[$i]['zipcode'] = $row['zipcode'];
		$reportLines[$i]['phone'] = $row['phone'];
		$reportLines[$i]['cardnumber'] = $row['cardnumber'];
		$reportLines[$i]['amountoutstanding'] = $row['amountoutstanding'];
		$reportLines[$i]['categorycode'] = $row['categorycode'];
		$reportLines[$i]['contactname'] = $row['contactname'];
		$reportLines[$i]['dateofbirth'] = $row['dateofbirth'];
		$reportLines[$i]['sort1'] = $row['sort1'];
	//	$reportLines[$i]['mindate'] = $row['min(accountlines.date)'];
		$reportLines[$i]['maxdate'] = $row['max(accountlines.date)'];
		$reportLines[$i]['amountoutstanding'] = $row['sum(amountoutstanding)'];
		$reportLines[$i]['da0'] = $da0;
		$reportLines[$i]['da1'] = $da1;
		
		$sql2 = "SELECT 
					description, 
					date, 
					itemnumber, 
					accounttype, 
					borrowers.borrowernumber, 
					dispute, 
					min(amountoutstanding)
				FROM 
					borrowers, accountlines
				WHERE 
					accountlines.borrowernumber = borrowers.borrowernumber
				AND
					borrowers.borrowernumber = '" . $row['borrowernumber'] ."' 
				GROUP by itemnumber";
		
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
		
		// Perform Query
		$resultSet2 = dbquery($conn, $sql2);
		
		while ($row2 = read_db_assoc($resultSet2))
			{
			$row2 = stripslashes_deep($row2);
			$reportLines[$i]['description'] .= $row2['description']. " <br> ";
			$reportLines[$i]['dispute'] .= $row2['dispute']. " <br> ";
			$reportLines[$i]['feeitem'] .= $row2['min(amountoutstanding)'] ." <br> ";
			$reportLines[$i]['barcode'] .= $row2['date']. " <br> ";
			}
		$i++;
		}
	}
	
if ($reporttype == "shelf")
	{
	$total = 0.0;
	if ($sendout) $handle = fopen( $filesave , "w");
	
	foreach($reportLines as $row)
		{
		$out = "";
		if ( $row[amountoutstanding] >= $dollarcutoff )
			{
			$street = rtrim($row['streetaddress']);
			$street = str_replace("\n", " ", $street);
			
			$out = $out . ($row['surname']. "^ " .$row['firstname']. "^ " . $street . "^ ");
			$out = $out . ($row['city']. "^ " .$row['zipcode']. "^ ");
			$out = $out . ($row['phone']. "^ " .$row['borrowernumber']. "^ " . $row['cardnumber'] . "^ ");
			$out = $out . ($row['amountoutstanding']. "^ " .$row['categorycode']. "^ " . $row['contactname'] . "^ ");
			$out = $out . ($row['dateofbirth']. "^ " .$row['sort1']. "^ " . $row['maxdate']);
			if ($sendout) $out = $out . ("\n");
				else $out = $out . ("<br>");
			if (! $sendout) echo $out;
			else 
				if (fwrite($handle, $out) === FALSE) 
					{
					echo "Cannot write to file ($filename)";
					exit;
					}
			}
		}
	if ($sendout) fclose($handle);
	}
else			// Send report in readable format
	{
	echo "<hr><center><h2>All Patrons Below Need $10 Unique-Fee Added to Account</h2></center><hr>";
	echo "<hr><center><h3>(if not already added--do *not* add $10 fee if recently included)</h3></center><hr>";
	echo '<table style="text-align: left; width: 100%;" border="0"
		cellpadding="2" cellspacing="2">
		<tr bgcolor="lightgray">		
			<td><strong>Last Name</strong></td>
			<td><strong>First Name</strong></td>
			<td><strong>Street</strong></td>
			<td><strong>City</strong></td>
			<td><strong>Zip</strong></td>
			<td><strong>Phone</strong></td>
			<td><strong>Cardnumber</strong></td>
			<td><strong>Amount</strong></td>
			<td><strong>Fee Included</strong></td>
		</tr>';
	foreach($reportLines as $row)
		{
		echo "<tr>";
		$out = "";
		if ( $row[amountoutstanding] >= $dollarcutoff )
			{
			$street = rtrim($row['streetaddress']);
			$street = str_replace("\n", " ", $street);
			echo "<td>" . $row['surname']. "</td>";
			echo "<td>" . $row['firstname']. "</td>";
			echo "<td>" . $street. "</td>";
			echo "<td>" . $row['city']. "</td>";
			echo "<td>" . $row['zipcode']. "</td>";
			echo "<td>" . $row['phone']. "</td>";
//			echo "<td>Click for patron<br>" . $row['cardnumber']. "</td>";
			echo '<td>Click for account<br><a href="'.$addrkoha433.$more_member.stripslashes($row['borrowernumber']).'">'.$row['cardnumber'].'</a> </td>';
			echo "<td><b>" . $row['amountoutstanding']. "</b></td>";
			echo "<td>" . $row['unique']. "</td>";
			/*$out = $out . ($row['surname']. "^ " .$row['firstname']. "^ " . $street . "^ ");
			$out = $out . ($row['city']. "^ " .$row['zipcode']. "^ ");
			$out = $out . ($row['phone']. "^ " .$row['borrowernumber']. "^ " . $row['cardnumber'] . "^ ");
			$out = $out . ($row['amountoutstanding']. "^ " .$row['categorycode']. "^ " . $row['contactname'] . "^ ");
			$out = $out . ($row['dateofbirth']. "^ " .$row['sort1']. "^ " . $row['maxdate']);
			if ($sendout) $out = $out . ("\n");
				else $out = $out . ("<br>");
			if (! $sendout) echo $out;
			else 
				if (fwrite($handle, $out) === FALSE) 
					{
        			echo "Cannot write to file ($filename)";
        			exit;
					}
			*/
			echo "</tr>";
			}
		
		}
	}
// close database
disconnect($conn);
?>
