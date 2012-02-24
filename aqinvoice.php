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
<form action="aqinvoice.php" method="GET" name="myform">
  	<div align="left">
  	  <h3><a href="http://koha.smfpl.org/cgi-bin/koha/acqui/acqui-home.pl">Acquisitions Home</a></h3>
  	</div>
  	<h2>Acquisitions Search </h2>
  	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2" align=right><strong>Invoice: 
          </strong>
        <input name="invoice" type="text" id="invoice" size="30" /></td>
        <td width="39%">&nbsp;</td>
      </tr>
      <tr>
        <td align=right>&nbsp;</td>
        <td width="14%" align=right><input type="submit" value="Submit Information" /></td>
        <td>&nbsp; </td>
      </tr>
    </table>
</form>
</center>

<?php
refocus("myform", "title");

// Create short variable names for form parameters
// Get parameters
$_GET = stripslashes_deep($_GET);
$invoice		= $_GET['invoice'];

if ($invoice == NULL)
	{
	die('<center><b>You must enter some search criteria</b></center>');
	}

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Build the Query
$query = "SELECT 
* 
FROM 
aqorders, aqorderbreakdown, aqbasket, aqbooksellers
WHERE
aqorders.booksellerinvoicenumber = '". $invoice ."'
AND
aqorderbreakdown.ordernumber = aqorders.ordernumber
AND
aqorders.basketno = aqbasket.basketno
AND
aqbooksellers.id = aqbasket.booksellerid
";

// Query the database
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<h3>Matching Invoices For: $invoice</h3>";

echo '<table border=1>';
echo 	"<tr> 
		<th>Title<br /><i>click to view</th>
		<th>OrderNo.<br /><i>click to edit</i></th>
		<th>Quan.</th> 
		<th>List Price</th>
		<th>Unit Price</th>
		<th>Date Created</th>
		<th>Date Recv.</th>
		<th>InvoiceNo.</th> 
		<th>Freight</th> 
		<th>Qnt. Recv'ed</th> 
		<th>Cancel Date</th> 
		<th>Notes</th> 
		<th>Book Seller<br /><i>click vendor to receive<br />click url-goto vend. site</i></th> 
		<th>BsktNo.</th> 
		<th>Fund Id.</th> 
		</tr>";

$previnvno = "fnord"; $ftot = 0.0; $unittot = 0.0;
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
//	echo "<td> ". $row['title'] ." </td>";
	if ($row['biblionumber'])
		echo '<td width=200><b><a href="'.$addrkoha433 . $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a></b></td>';	
	else
		echo '<td width=200><b>'. $row['title'].'</b></td>';
//	echo "<td> ". $row['ordernumber'] ." </td>";
		echo "<td><b><a href='".$addrkoha433. $aqui_edit1 . $row['ordernumber']
											.$aqui_edit2. $row['id']
											.$aqui_edit3. $row['basketno'] .
							"'>". $row['ordernumber'] ."</a></td>";
	// http://koha.smfpl.org/cgi-bin/koha/acqui/neworderempty.pl?ordnum=13300&booksellerid=8&basketno=1534
	echo "<td> ". $row['quantity'] ." </td>";
	echo "<td> $ ". number_format(floatval($row['listprice']),2) ." </td>";
	echo "<td> $ ". number_format($row['unitprice'],2) ." </td>";
	echo "<td> ". $row['creationdate'] ." </td>";
	echo "<td> ". $row['datereceived'] ." </td>";
	echo "<td><b>". $invoice ."</b></td>";
	$truefreight = 0;
	if ($previnvno != $row['booksellerinvoicenumber']) $truefreight=$row['freight'];
	echo "<td align=right> $ ". number_format($truefreight,2) ."</td>";
//	echo "<td> $ ". number_format($row['freight'], 2) ." </td>";
	echo "<td> ". $row['quantityreceived'] ." </td>";
	echo "<td> ". $row['datecancellationprinted'] ." </td>";
	echo "<td> ". $row['notes'] ." </td>";
	echo "<td><b>  <a href='". $addrkoha433 . $aqui_receive_link. $row['id'] . "'>". $row['name']  ."</a></b><br />"
		.$row[postal]."<br />"
		.$row[contact]."<br />"
		.$row[contphone]."<br />"
		."<a href='".$row[url]."'>".$row[url]."</a><br /></td>";
//	echo "<td> ". $row['basketno'] ." </td>";
	echo '<td><b><a href="'.$addrkoha433 . $aqui_basket_link . $row['basketno'] .'">'.$row['basketno'].'</a><b/></td>';	
	echo "<td> ". $row['bookfundid'] ." </td>";
	echo "</tr>";
	$previnvno = $row['booksellerinvoicenumber'];
	$ftot += $truefreight;
	$unittot += $row['unitprice'];
	}
echo "<tr align=right>
		<td></td><td></td><td></td><td></td>
		<td><b>Total =<br />$ ". number_format($unittot,2) ."</b></td>
		<td></td><td></td><td></td>
		<td><b> + Freight =<br /><i>depreciated May09</i><br />$ ". number_format($ftot,2) ."</b></td>
		<td></td><td></td><td></td>
		<td><b>Grand Total with Pre 5/2009 freight method<br />$ ". number_format($unittot + $ftot,2) ."</b></td>
		<td></td><td></td>		
		</tr>";
echo "</table>";

// done with this database
disconnect($conn);
?>

