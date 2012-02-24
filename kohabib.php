<?php
## BYPASS SEARCH, Performs MySQL search only

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

## This report generates lists of overdue items to use for shelfchecking
## and pdf's of overdue notices to be printed out and mailed to the borrower.
## This file displays a form to get the settings for the overdues.
## Note: This one is a bit messy and could use cleaning up, switch to templates.
## @package koha-tools
## @subpackage koha-reports
## @author D. Ulm
## @copyright 2008

// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
// Print the main top menu
require_once("topmenu.php");

// Get input for search
?>


<center>
<form action="kohabib.php" method="POST" name="myform">
  	<h2>Quick Catalogers Biblio Only Search</h2>
  	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="8%" align=right><strong>Title</strong></td>
        <td width="25%" align=right><input name="title" type="text" id="title" size="30" /></td>
        <td width="16%" align=right><strong>Abstract </strong></td>
        <td width="51%"><input name="abstract" type="text" id="abstract" size="50" /></td>
      </tr>
	  <tr>
        <td align=right><strong>Author</strong></td>
        <td align=right><input name="author" type="text" id="author" size="30" /></td>
        <td align=right><strong>Series</strong></td>
        <td><input name="series" type="text" id="series" size="30" /></td>
	  </tr>
      <tr>
        <td align=right><strong>ISBN</strong></td>
        <td align=right><input name="isbn" type="text" id="isbn" size="20" /></td>
        <td align=right>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align=right>&nbsp;</td>
        <td align=right><input name="images" type="checkbox" id="images" value="1" checked />
          <strong>Images </strong>(slower)</td>
        <td align=right><input type="submit" value="Submit Information" /></td>
        <td>&nbsp; </td>
      </tr>
    </table>
</form>
</center>

<?php
refocus("myform", "title");

// Create short variable names for form parameters
// Get parameters
$_POST = stripslashes_deep($_POST);
$title		= $_POST['title'];
$barcode	= $_POST['barcode'];
$author		= $_POST['author'];
$isbn		= $_POST['isbn'];
$series		= $_POST['series'];
$abstract	= $_POST['abstract'];
$images		= $_POST['images'];

if ($title == NULL && $author==NULL && $series==NULL && $abstract==NULL && $isbn==NULL)
	{
	die('<center><b>You must enter some search criteria</b></center>');
	}

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Build the Query
$query = build_koha_search($title, $author, $series, $abstract, $isbn);

// Query the database
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo '<h3>Biblios Matching:</h3>';

echo '<table border=1>';
echo 	"<tr> 
		<th>Title<br><i>Display</i></th>
		<th>Image</th>
		<th>Bib<br><i>Edit</i></th> 
		<th>Items<br><i>Edit</i></th>
		<th>Marc<br><i>View</i></th>
		<th>Author</th> 
		<th>Notes</th> 
		<th>Series Title</th> 
		<th>Copyright</th> 
		<th>Date Created</th> 
		<th>Abstract</th> 
		<th>ISBN</th> 
		<th>Publisher</th> 
		<th>Illus.</th>
		<th>Pages</th>
		<th>Size</th>
		<th>Place</th>
		<th>Date</th>
		</tr>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo '<td> <a href="'.$addrkoha433 . $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a> </td>';	
	if ($images)
			{
			$isbntrim = strtok(trim($row['isbn'])." ", " :(");
			echo '<td> <center> <img src="http://images.amazon.com/images/P/'. $isbntrim .'.01.TZZZZZZZ.jpg" > </center> </td>'; 
			}
	else	
			echo '<td> </td>';
	echo '<td> <a href="'.$addrkoha433 . $edit_bib. $row['biblionumber'] .'">'.$row['biblionumber'].'</a> </td>';	
	echo '<td> <a href="'.$addrkoha433. $edit_item . $row['biblionumber'] .'">'."Items".'</a> </td>';	
	echo '<td> <a href="'.$addrkoha433. $view_marc . $row['biblionumber'] .'">'."Marc".'</a> </td>';	
	echo "<td> ". $row['author'] ." </td>";
	echo "<td> ". $row['notes'] ." </td>";
	echo "<td> ". $row['seriestitle'] ." </td>";
	echo "<td> ". $row['copyrightdate'] ." </td>";
	$formatdate = date("F j, Y",strtotime($row['datecreated']));
	echo "<td> ". $formatdate ." </td>";
	echo "<td> ". $row['abstract'] ." </td>";
	echo "<td> ". $row['isbn'] ." </td>";
	echo "<td> ". $row['publishercode'] ." </td>";
	echo "<td> ". $row['illus'] ." </td>";
	echo "<td> ". $row['pages'] ." </td>";
	echo "<td> ". $row['size'] ." </td>";
	echo "<td> ". $row['place'] ." </td>";
	echo "<td> ". $row['datecreated'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

// done with this database
disconnect($conn);
?>

