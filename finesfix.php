<?php
## Fines Fix By Patron ID 

## Copyright 2008 Darrell Ulm

## This file is part of koha-extras.

## koha-extras is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## koha-extras is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with koha-extras; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
// Print the main top menu
require_once("topmenu.php");

// Input Value
echo '<center><form action="finesfix.php" method="POST" name="myform">
	<h1>Fines Fix Report for Patron </h1>
	<h3> <input type="submit" value="Submit one row:" /></h3>
	<table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><div align=right> Patron Last Name: </div></td>
        <td><div align=left><input name="lname" type="text" id="lname" size="20" /></div></td>
        <td><div align=right> Patron First Name: </div></td>
        <td><div align=left><input name="fname" type="text" id="fname" size="20" /></div></td>
      </tr>
	  <tr>
        <td><div align=right> Cardnumber: </div></td>
        <td><div align=left><input name="cardno" type="text" id="cardno" size="20" /></div></td>
      </tr>
	  <tr>
        <td><div align=right> Receipt Code on a receipt: </div></td>
        <td><div align=left><input name="patid" type="text" id="patid" size="20" /></div></td>
      </tr>
    </table>
	</form>
	</center>';

	refocus("myform", "patid");

// create short variable names for form parameters
// Get parameters
$patid		= stripslashes($_POST['patid']);
$cardno		= stripslashes($_POST['cardno']);
$fname		= stripslashes($_POST['fname']);
$lname		= stripslashes($_POST['lname']);
if ($patid == NULL && $cardno == NULL && $fname == NULL && $lname == NULL )
	{
	die('<center><b>You must enter some search criteria</b></center>');
	}

echo '<center><h2><i> List of Patrons Matching </i></h2></center>';

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//build query
$query = "select * from borrowers where 
    borrowernumber IS NOT NULL ";
if ($patid) $query .=	" and borrowernumber = '".$patid."' ";
if ($cardno) $query .=	" and cardnumber = '".$cardno."' ";
if ($lname) $query .=	" and surname LIKE '%".$lname."%' ";
if ($fname) $query .=	" and firstname LIKE '%".$fname."%' ";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("<center>no results found<center>");
if (numrows($result) > 1) die("<center>More than on patron found, narrow results<center>");

echo "<center><table border=1>";
echo "<tr>
	<th>Patron Record</th>
	<th>Lastname</th>
	<th>Firstname</th> 
	<th>Patron ID</th>
	<th>Card Number</th>";
/*
	<th>Street Address</th>
	<th>City</th> 
	<th>Phone</th> 
	<th>Birthday</th> 
	<th>M-F</th> 
	<th>Zipcode</th>";
*/
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo '<td> <a href="'.$addrkoha433. $more_member . stripslashes($row['borrowernumber']) .'">'."Click to go<br>to Patron Record".'</a> </td>';	
	echo "<td> ". $row['surname'] ." </td>";
	echo "<td> ". $row['firstname'] ." </td>";
	echo "<td> ". $row['borrowernumber'] ." </td>";
	$bnumber = $row['borrowernumber'];
	echo "<td> ". $row['cardnumber'] ." </td>";
/*	echo "<td> ". $row['address'] ." </td>";
	echo "<td> ". $row['city'] ." </td>";
	echo "<td> ". $row['phone'] ." </td>";
	echo "<td> ". $row['dateofbirth'] ." </td>";
	echo "<td> ". $row['sex'] ." </td>";
	echo "<td> ". $row['zipcode'] ." </td>";
*/	echo "</tr>";
	}
echo "</table></center><hr><br />";

//build query
$query = "SELECT * 
FROM accountlines, borrowers
WHERE 
borrowers.borrowernumber = '". $bnumber ."' 
AND
borrowers.borrowernumber = accountlines.borrowernumber
order by date";

$result = dbquery($conn, $query);
if (numrows($result) == 0) die("<center>no fines found<center>");

echo "<center><table border=1>";
echo "<tr>
	<th>Date</th>
	<th>Description</th>
	<th>Dispute</th>
	<th>Account Type</th>
	<th>Itemnumber</th>
	<th>Amount</th>
	<th>Amount Outstanding</th>
	<th>Replacement Price</th>
	<th>Amount Outstanding<br />MAX'ed at Rep. Price</th>
	";

$amt_tot = 0.0;
$amtout_tot = 0.0;
$max_tot = 0.0;
$zeroout = 0;

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['date'] ." </td>";
	
	echo "<td> ". $row['description'] ." </td>";
	echo "<td> ". $row['dispute'] ." </td>";
	echo "<td> ". $row['accounttype'] ." </td>";
	$item_pos = strpos($row['description'], "[item: ");
	$newitem = substr($row['description'], -(strlen($row['description']) - $item_pos - 7));
	$newitem = str_replace(" ]", "", $newitem);
	$newitem = intval($newitem);
	if ($newitem == 0) $newitem = "";
	if ( $row['itemnumber'] <> "" ) $itemno =  $row['itemnumber'];
		else $itemno = $newitem;
	echo "<td> ". $itemno ." </td>";

	$fmt_amount = sprintf("$ %14.2f", $row['amount']);
	echo "<td> ". $fmt_amount ." </td>";
	$fmt_amountout = sprintf("$ %14.2f", $row['amountoutstanding']);
	echo "<td> ". $fmt_amountout ." </td>";	

	//build query for replacement price
	$query2 = "SELECT * 
		FROM items
		WHERE 
		items.itemnumber = '". $itemno ."' ";
	$result2 = dbquery($conn, $query2);
	$row2 = read_db_assoc($result2);
	$row2 = stripslashes_deep($row2);
	$replacementprice = $row2['replacementprice'];
	echo "<td> ". $replacementprice ." </td>";

	$amt_tot += $row['amount'];
	$amtout_tot += $row['amountoutstanding'];
	if ($row['amountoutstanding'] <= $replacementprice || $replacementprice == 0 )
		{
		$max_amt = $row['amountoutstanding'];
		$rowcol1 = ""; 		$rowcol2 = "";
		}
	else
		{
		$max_amt = $replacementprice;
		$rowcol1 = "<b><i>"; 		$rowcol2 = "</i></b>";
		}
	echo "<td> ". $rowcol1 . sprintf("$ %14.2f", $max_amt) . $rowcol2 ."</td>";
	$max_tot += $max_amt;
	echo "</tr>";
	
	if ($amtout_tot == 0.0 && $zeroout == 1)
		{
		$zeroout = 0;
		echo "<tr><td></td><td><b>At this date account was paid off.</b></td></tr>";
		}
	else
		$zeroout = 1;

	}

echo "<hr>";
	echo "<tr>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td><b> ". sprintf("$ %14.2f", $amt_tot)." </b></td>";
	echo "<td><b> ". sprintf("$ %14.2f", $amtout_tot) ." </b></td>";
	echo "<td></td>";
	echo "<td><b> ". sprintf("$ %14.2f", $max_tot) ." </b></td>";
	echo "</tr>";
	
echo "</table></center>";

// close database
disconnect($conn);
?>
