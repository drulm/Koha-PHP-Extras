<?php
## ILL Search 

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

// Below is the to search for ILL
?>
<center>
  <form action="illsearch.php" method="POST" name="myform">
    <p><strong><font size="5">ILL Search</font></strong></p>
	<hr>
    <table width="70%" cellpadding="4" cellspacing="4" >
    <tr align="center" valign="middle">
      <td height="37"><div align="right"><strong>Search Type          
                  <select name="command" size="1" id="command">
                    <option>TITLE</option>
                    <option>PATRON NAME</option>
                    <option>BARCODE</option>
                  </select>
        </strong></div></td>
      <td><div align="left"><strong> Value          
            <input name="stitle" type="text" size="30" maxlength="30" />        
        </strong></div></td>
    </tr>
    <tr align="center" valign="middle">
      <td height="37" colspan="2"><div align="center"><strong>Historical ILL 
        
        <input name="history" type="checkbox" id="history" value="1" />
      </strong>        (barcode only) 
      </div></td>
      </tr>
  </table>
    <h1><font size="4"><input type="submit" name="Submit" value="Search" />
    </font></h1>
  </form>
<b></b><b>
</b>
</center>

<?php
refocus("myform", "stitle");

// Start code to obtain search
$stitle		= trim($_POST['stitle']);
$command	= trim($_POST['command']);
$history	= trim($_POST['history']);

$limitset = " ";
if ($history == 0) $limitset = " limit 1 "; 

// show the information provided
if ( $stitle ) echo '<center><h3> You Entered the Following Search Value: <i>'.$stitle.'</i></h3></center>';
	else die('<center><h4>Please enter a search term.</h4></center>');
	
// Open the "local" database
$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);

$mod_title = '%' . mysql_real_escape_string($stitle) . '%';

if ($command == 'TITLE')
	$query = sprintf("select * from listill WHERE title LIKE '%s' ORDER BY date DESC, time DESC", $mod_title);
else if ($command == 'BARCODE')
	$query = sprintf("select * from listill WHERE barcode LIKE '%s' ORDER BY date DESC, time DESC %s",$mod_title,$limitset);
else if ($command == 'PATRON NAME')
	$query = 'select * from listill WHERE name LIKE "'.$mod_title.'" ORDER BY date DESC, time DESC ';

// Perform Query
$result = dbquery($conn, $query);
$num_rows = numrows($result);
if (numrows($result) == 0) 
		die("<center><h4>This please return to previous screen</h4></center>");
echo "There are <i><b> $num_rows </b></i> Entries <br><br>";

echo "<table border=1>";
echo "<tr><th>title</th><th>collection</th><th>callno</th><th>itype</th><th>Temp ILL barcode</th> 
		<th>patronid</th><th>patron name</th><th>time</th><th>date</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td><b> ". $row['title'] ." </b></td>";
	echo "<td> ". $row['collection'] ." </td>";
	echo "<td> ". $row['callno'] ." </td>";
	echo "<td> ". $row['itype'] ." </td>";	
	echo "<td> ". $row['barcode']." </td>";
	echo '<td> <b> <a href="'.$addrkoha433 . $more_member . $row['patronid'] .'">'.$row['patronid'].'</a> </b> </td>';
	echo "<td> ". $row['name'] ." </td>";	
	$formatdate = date("g:i A",strtotime($row['time']));
	echo "<td> ". $formatdate ." </td>";
	$formatdate = date("m/d/Y",strtotime($row['date']));
	echo "<td> ". $formatdate ." </td>";
	echo "</tr>";
	}
echo "</table>";

// close database
disconnect($conn);
?>
