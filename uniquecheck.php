<?php

$debug = 0;

ini_set("memory_limit","256M");
ini_set('include_path',ini_get('include_path').';C:\Program Files\xampp\xampp\htdocs\KohaTools\classes\;');

require_once('MySQLConnectionFactory.class.php');
require_once('Calendar.class.php');
require_once('fpdf/fpdf.php');
require_once('BillPdf.class.php');
require_once('KohaObjects_Borrower.class.php');

$library = $_POST['library'];
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
$reporttype = "shelf";


$borrowerTypes = array('PA','YV','TE','NV','ST','IL','OR');
//Grab all the borrower types that are to be included in the report
$i = 0;
$selectedTypes = array();
foreach($borrowerTypes as $key){
	if (isset($_POST[$key])) {$selectedTypes[$i++] = $_POST[$key];}
}

$sendout 		= 0;

$itypearray = explode(" ", $itypelist );

$dayofweek = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")));

// holiday stuff
//connect to local db
$link = mysql_connect('192.168.0.176', 'root', '86punish');
if (!$link) 
	{
	die('<br>Could not connect to ILL database, please try again later.<br>' . mysql_error());
	}
$db = mysql_select_db("ill", $link);
if (!$db) 
	{
	die ('<br>Can\'t use ILL database.<br>' . mysql_error());
	}

$query = sprintf("select 	* from `ill`.`holiday` 
where `holdate` = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
or `holdate`  = DATE_SUB(CURDATE(), INTERVAL 2 DAY)
or `holdate`  = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
or `holdate`  = DATE_SUB(CURDATE(), INTERVAL 4 DAY)
or `holdate`  = DATE_SUB(CURDATE(), INTERVAL 5 DAY)
or `holdate`  = DATE_SUB(CURDATE(), INTERVAL 6 DAY)");
	// Perform Queries
	$result = mysql_query($query);
	$nrows = mysql_num_rows($result);
mysql_close($link);

$week = array(1,0,0,0,0,0,1); 		// days of week...1 means "holiday"

for ($i=0; $i < $nrows; $i++)
	{
	$hrow = mysql_fetch_array($result);
	$harr = stripslashes($hrow['holdate']) ;
	$j = jddayofweek ( cal_to_jd(CAL_GREGORIAN, (int)substr($harr,5,2), (int)substr($harr,8,2), (int)substr($harr,0,4)));
	$week[$j] = 1;
	}

//count backwards til we hit a 0 (non holiday) modulo 7
$cd = $dayofweek + 7 - 1;	// start + 7 for % and - 1 for prev day
$hols = 0;					// zero holidays so far
while ($week[$cd%7] != 0 && $hols<7)
	{
	$cd = $cd - 1;
	$hols++;
	}
if ($holidays == 0)				// check for override --- usually 0
	$holidays = $hols; 			// works, lets set it

//---------------------------END OF HOLIDAY STUFF----------------------------------


//$dvd_days_ago = 7;

$dbh = MySQLConnectionFactory::create();

//Get the data about the library from the database.
$sql = 'SELECT * FROM branches WHERE branches.branchcode = \'' . $library . '\'';
$resultSet = mysql_query($sql) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$num_rows = mysql_num_rows($resultSet);
if (!$resultSet) 
	{
   $message  = 'Invalid query: ' . mysql_error() . "\n";
   $message .= 'Whole query: ' . $sql;
   die($message);
	}

$row = mysql_fetch_array($resultSet);
$libraryData = array();
$libraryData['name'] = $row['branchname'];
$libraryData['address1'] = $row['branchaddress1'];
$libraryData['address2'] = $row['branchaddress2'];
$libraryData['address2'] = $row['branchaddress2'];
$libraryData['phone'] = $row['branchphone'];
$libraryData['fax'] = $row['branchfax'];
$libraryData['email'] = $row['branchemail'];

if($daysago1 > $daysago2){
	$daysAgo[0] = $daysago1 + $holidays;
	$daysAgo[1] = $daysago2;
} else {
	$daysAgo[0] = $daysago2 + $holidays;
	$daysAgo[1] = $daysago1;
}

//Check to make sure all neccessary data exists
$anyErrors = false;
//if ($lname == "" && $fname == ""){
//	echo "<p2>You must type in at least one Name</p2><br>";
//	$anyErrors = true;
//}
if (count($selectedTypes) < 1){
	echo "You must choose at least one Patron Type<br>";
	$anyErrors = true;
}
if (count($daysAgo) < 2 ) {
	echo "You must fill in both 'Days Ago' boxes<br>";
	$anyErrors = true;
}
if($anyErrors){
	echo "Please use your browser's back button to return to the last page and fill in the missing data.<br>";
	echo "<br>Thank you<br>";
	die();
	}

//Grab the data from the database
$myCal = new Calendar();

$sql = 'SELECT 
		GROUP_CONCAT(accounttype ORDER BY accountlines.date DESC SEPARATOR " <br> "),
		GROUP_CONCAT(description ORDER BY accountlines.date DESC SEPARATOR " <br> "),
		GROUP_CONCAT(amountoutstanding ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		GROUP_CONCAT(accountlines.date ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		GROUP_CONCAT(itemnumber ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		GROUP_CONCAT(accounttype ORDER BY accountlines.date DESC SEPARATOR " <br> "), 
		
		count(*), max(accountlines.date), min(accountlines.date), 
		sum(amountoutstanding), 

		borrowers.borrowernumber, borrowers.surname, borrowers.firstname, borrowers.dateofbirth,
		borrowers.streetaddress, borrowers.city, borrowers.phone, borrowers.zipcode,
		borrowers.cardnumber, borrowers.categorycode, borrowers.sort1, 
		borrowers.streetaddress, borrowers.contactname,
		itemnumber,description, amountoutstanding, accountlines.date
		
		FROM borrowers, accountlines
	
		WHERE 
		accountlines.borrowernumber = borrowers.borrowernumber ';
		
		if (strcmp($lname,"") != 0) $sql .= ' AND borrowers.surname LIKE "%'. $lname . '%" ';
		if (strcmp($fname,"") != 0) $sql .= ' AND borrowers.firstname LIKE "%' . $fname . '%" ';
		
		$sql .= ' AND ' . getCategoryCodesSql($selectedTypes) ." ";

		$sql .= ' 		
		GROUP BY accountlines.borrowernumber
		HAVING sum(amountoutstanding) >= ' . $dollarcutoff . ' ';
		
//-------------------------------------------------------------------------------------------------------
$resultSet = mysql_query($sql) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

// modify with these entries from overdue notices...
$today = strtotime("now"); 
$daysago0 = $today - $daysAgo[0] * 86400;
$daysago1 = $today - $daysAgo[1] * 86400;
//echo "<br> Date: $today   daysago0: $daysago0  daysago1: $daysago1 <br>";

//do some sort of selection calculation here or sum by date...
$reportLines = array();
$i = 0;
while($row = mysql_fetch_array($resultSet))
	{
	$date_flag = 0;
	if ($row['sum(amountoutstanding)'] > $dollarcutoff)
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
			//echo $startdate."   ";
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
				//echo "<br> $da0 -- $chd -- $da1 <br>";
				$date_flag=1;
				}
			$ti++;
			}
		if (! $sendout && $debug)
			echo "name: ".$row['surname'].",".$row['firstname']." cardnumber:".$row['cardnumber']." pre:$prefines  post:$postfines   allfines:$allfines\n<br>";
		if ($prefines >= $dollarcutoff || $postfines <= $dollarcutoff) // dont send a bill, we already sent one!!!
			$date_flag=0;
		}
	
	if ($date_flag == 1) 
		{
		$reportLines[$i] = array();
		$reportLines[$i]['borrowernumber'] = $row['borrowernumber'];
		
		switch($reporttype){
			case "shelf":
				$reportLines[$i]['chargecount'] = $row['itemnumber'];
				$reportLines[$i]['unique'] = $uniquefee;
				$reportLines[$i]['prefines'] = $prefines;
				$reportLines[$i]['postfines'] = $postfines;
				$reportLines[$i]['allfines'] = $allfines;
				$reportLines[$i]['surname'] = $row['surname'];
				$reportLines[$i]['firstname'] = $row['firstname'];
				$reportLines[$i]['streetaddress'] = $row['streetaddress'];
				$reportLines[$i]['city'] = $row['city'];
				$reportLines[$i]['zipcode'] = $row['zipcode'];
				$reportLines[$i]['phone'] = $row['phone'];
				$reportLines[$i]['cardnumber'] = $row['cardnumber'];
				$reportLines[$i]['amountoutstanding'] = $row['amountoutstanding'];
				$reportLines[$i]['categorycode'] = $row['categorycode'];
				$reportLines[$i]['contactname'] = $row['contactname'];
				$reportLines[$i]['dateofbirth'] = $row['dateofbirth'];
				$reportLines[$i]['sort1'] = $row['sort1'];
				$reportLines[$i]['mindate'] = $row['min(accountlines.date)'];
				$reportLines[$i]['maxdate'] = $row['max(accountlines.date)'];
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
		itemnumber, 
		accounttype, 
		borrowers.borrowernumber, 
		dispute, 
		min(amountoutstanding)
		
		FROM borrowers, accountlines
	
		WHERE 
		accountlines.borrowernumber = borrowers.borrowernumber
		and
		borrowers.borrowernumber = '" . $row['borrowernumber'] ."' 
		
		GROUP by itemnumber
		";
		
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
		$resultSet2 = mysql_query($sql2) or die("<b>(2!) A fatal MySQL error occured</b>.\n<br />Query: " . $sql2 . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		while($row2 = mysql_fetch_array($resultSet2))
			{
			switch($reporttype){
				case "shelf":
					$reportLines[$i]['description'] .= $row2['description']. " <br> ";
					$reportLines[$i]['dispute'] .= $row2['dispute']. " <br> ";
					$reportLines[$i]['feeitem'] .= $row2['min(amountoutstanding)'] ." <br> ";
					$reportLines[$i]['barcode'] .= $row2['date']. " <br> ";
					break;
				case "mail":
					$reportLines[$i]['author'] .= $row2['itemnumber']. " <br> ";
					$reportLines[$i]['title'] .= $row2['description']. " <br> ";
					$reportLines[$i]['itemcallnumber'] .= $row2['accounttype']. " <br> ";
					$reportLines[$i]['price'] .= $row2['min(amountoutstanding)']. " <br> ";
					$reportLines[$i]['timestamp'] .= $row2['min(amountoutstanding)']. " <br> ";
					$reportLines[$i]['date_due'] .= $row2['date']. " <br> ";		
					break;
				}
			}
		$i++;
		}
	}
	
	{
	$total = 0.0;
	if ($sendout) $handle = fopen( $filesave , "w");
	/*
		echo '<table style="text-align: left; width: 100%;" border="0"
		cellpadding="2" cellspacing="2">
		<tr bgcolor="lightgray">
			<td><strong>Charges</strong></td>		
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
}*/
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
			echo '<td>Click for account<br><a href="http://66.213.124.205:443/cgi-bin/koha/boraccount.pl?bornum='.stripslashes($row['borrowernumber']).'">'.$row['cardnumber'].'</a> </td>';
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
echo "<hr><center>Done</center><hr>";
}

function hashDataByBorrowernumber($reportLines){
	$currentBorrower = null;
	$hashedArray = array();
	foreach($reportLines as $thisLine){
		//echo "-".$thisLine['borrowernumber'];
		if ($currentBorrower == $thisLine['borrowernumber']){
			$hashedArray[$currentBorrower]['itemsDue'][] = $thisLine;
		} else {
			$currentBorrower = $thisLine['borrowernumber'];
			$hashedArray[$currentBorrower] = array();
			$hashedArray[$currentBorrower]['itemsDue'] = array();			
			$hashedArray[$currentBorrower]['itemsDue'][] = $thisLine;

			//Get this borrowers info
			$thisBorrower = new KohaObjects_Borrower();
			$thisBorrower->get($currentBorrower);
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