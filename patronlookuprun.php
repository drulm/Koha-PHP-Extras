<?php
## Patron Lookup By Patron ID 

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
?>
<center><form action="patronlookuprun.php" method="POST" name="myform">
  <h2 align="center">Check Patron ID</h2>
  <div align="center">
    <table align="center">
        <tr>
          <td width="366" height="57"><strong>First Name </strong>
          <input name="fname" type="text" id="fname" size="30" /></td>
          <td width="366"><strong>Last Name 
            <input name="lname" type="text" id="lname" size="30" />
          </strong> </td>
        </tr>
        <tr>
          <td height="57"><strong>Barcode
            <input name="patid" type="text" id="patid" size="30" />
          </strong></td>
          <td><input type="submit" value="Submit Information" /></td>
        </tr>
      </table>
    </div>
    </form>
</center>
<?php
refocus("myform", "fname");

// create short variable names for form parameters
// Get parameters
$patid		= stripslashes($_POST['patid']);
$fname		= stripslashes($_POST['fname']);
$lname		= stripslashes($_POST['lname']);

if ($patid == NULL && $fname == NULL && $lname == NULL)
	die('<br><b>You must enter some search criteria.</b><br>');

echo '<center><h2><i> List of Patrons Matching -- ADULT LAB</i></h2>';

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select *, (YEAR(CURDATE())-YEAR(dateofbirth))-(RIGHT(CURDATE(),5)<RIGHT(dateofbirth,5)) AS age
		from borrowers where borrowernumber IS NOT NULL ";
if ($patid != NULL) $query .= " and cardnumber = ".$patid."  ";
if ($lname != NULL) $query .= " and surname LIKE '%".$lname."%' ";
if ($fname != NULL) $query .= " and firstname LIKE '%".$fname."%' ";
$query .= " order by surname, firstname limit 0,50 ";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("No patrons found");

echo "<table border=1>";
echo "<tr>
	<th>Patron Record</th>
	<th>Lastname</th>
	<th>Firstname</th>
	<th>Age</th>
	<th>YES/NO NET</th>
	<th>Card Number<br />LINK TO FINES RECORD</th>";
if ($patronage_full)
	echo "	<th>Street Address</th>
		<th>City</th>
		<th>Phone</th>
		<th>Birthday</th>
		<th>M-F</th>
		<th>Zipcode</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$yesnet = substr($row['borrowernotes'],0, 6);
	if ( $row['age'] < 12 && $row['dateofbirth'] != NULL || $smfpl_yes && strncmp($yesnet,"NONET",5)==0 )
		echo '<tr bgcolor="#FF8888">';
	else if ( $row['dateofbirth'] == NULL)
		echo '<tr bgcolor="#FFFF00">';
	else
		echo '<tr bgcolor="#88FF88">';
	echo "<td><b>". $row['categorycode'] ."</b></td>";	
	echo "<td> ". $row['surname'] ." </td>";
	echo "<td> ". $row['firstname'] ." </td>";
	echo "<td><b> ";
	if ( $row['age'] < 12 ||  $row['dateofbirth'] == NULL)
		echo " <font color='red'> ";
	else
		echo " <font color='green'> ";
	if ( $row['age'] != NULL )
		echo $row['age'] ." <font></b></td>";
	else
		echo "DOB is not on file.<br>Patron should see circulation.  <font></b></td>";
	echo "<td><b> ". $yesnet ." </b></td>";
	echo "<td>";
	echo '<center><form action="finesfix.php" method="POST" name="myform">
	 <input type="submit" value="Lookup Fines:" />
         <div align=right> Cardnumber: </div>
        <div align=left><input name="cardno" type="text" id="cardno" size="20" readonly value='.$row['cardnumber'].' /></div>
	</form>
	</center>';
	echo "</b></td>";
	if ($patronage_full)
		{
		echo "<td> ". $row['streetaddress'] ." </td>";
		echo "<td> ". $row['city'] ." </td>";
		echo "<td> ". $row['phone'] ." </td>";
		echo "<td><b> ". $row['dateofbirth'] ." </b></td>";
		echo "<td> ". $row['sex'] ." </td>";
		echo "<td> ". $row['zipcode'] ." </td>";
		}
	echo "</tr>";
	}
echo "</table></center>";

// close database
disconnect($conn);
?>

