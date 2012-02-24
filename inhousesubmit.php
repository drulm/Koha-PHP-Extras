<?php
## In house Use

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

<form action="inhousesubmit.php" method="POST" name="myform">
  <h1>Enter In House Circ</h1>

  <table width="66%" >
    <tr>
      <td width="37%" height="54"><h2 align="right"><strong>Count</strong> </h2>
      </td>
      <td width="63%"><font size="+3">
        <input name="count" type="text" id="count" size="20" maxlength="50" />
      </font></td>
    </tr>
    <tr>
      <td height="61"><h2 align="right"><strong>Staff Name </strong></h2></td>
      <td><font size="+3">
      <input name="staffname" type="text" id="staffname" size="20" maxlength="30" />
      </font></td>
    </tr>
    <tr>
      <td>
        <div align="left">        </div></td>
      <td>
        <div align="left">
          <input type="submit" value="SUBMITILL" />
        </div></td>
    </tr>
  </table>
<?php
$itemcount		= addslashes($_POST['count']);
$staffname		= addslashes($_POST['staffname']);

echo '<font color="red">';
$illerr = 0;
echo '<center><h4>';
if ( !$itemcount )
	{
	echo '<br> Missing <b>Count</b><br>';
	$illerr++;
	}
if ( !$staffname )
	{
	echo ' Missing <b>Staff Name</b> <br>';
	$illerr++;
	}
if ($illerr != 0)
	{
	echo '<br> '.$illerr.' Fields Empty <br>';
	echo '<br> In House Count not Accepted <br>';
	refocus("myform", "count");
	exit;
	}
echo '</h4></center>';
echo '</font>';

// show the information provided
echo '<h3> You Entered the Following Information: </h3>';
echo '<h4>';
echo '<table><b>';
echo '<tr><td align=right><b>Count:</b></td><td> '.$itemcount.'</td></tr>';
echo '<tr><td align=right><b>Staff Name:</b></td><td> '.$staffname.'</td></tr>';
echo '</b></table>';

// Open the "local" database
$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);

// Insert the ILL record into table
$sql = "INSERT INTO inhouseuse SET 
		houseuse='$itemcount', 
		staffname='$staffname'" ;

// Insert the items into the local ILL database
$result = dbquery($conn, $sql);
echo "<hr><h2><center>Usage data is stored.</center></h2>";
refocus("myform", "count");
// close the web site
disconnect($conn);
?>
  