<?php
## Acqui. Budgets Items

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
  	<div align="left">
  	  <h3><a href="http://koha.smfpl.org/cgi-bin/koha/acqui/acqui-home.pl">Acquisitions Home</a></h3>
  	</div>
<?php
$startdate		= $_GET['startdate'];
$enddate		= $_GET['enddate'];
$bookfundid		= $_GET['bookfundid'];
$budget			= $_GET['budget'];

echo "<h3>Date Range: ( $startdate )  to  ($enddate)  </h3>";

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select * 
			from
			aqorders, aqorderbreakdown
			where 
			bookfundid = '". $bookfundid ."'
			AND aqorders.ordernumber = aqorderbreakdown.ordernumber
			AND quantityreceived IS NOT NULL
			AND (datecancellationprinted IS NULL OR datecancellationprinted='0000-00-00')
			AND datereceived IS NOT NULL 
			";

if ( where_clause($query) ) $query .= " and ";
if ($startdate != "") $query .= " datereceived >= ".'"'.$startdate.'"'."  ";
if ( where_clause($query) ) $query .= " and ";
if ($enddate != "") $query .= " datereceived <= ".'"'.$enddate.'"'."  ";

//$query .=	" group by aqorderbreakdown.bookfundid";

echo "<h2>Orders For Bookfund $bookfundid</h2><i>Use CTRL F to search for items</i>";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr align=right> 
<th>Title</th>
<th>Invoice No.</th>
<th>Date Received</th>
<th>Unit Price</th>
<th>Qnt. Rcvd.</th>
<th>Price All</th>
<th>Freight<br/><i>Depreciated<br />Prior method<br />before May 2009</th>
<th>Notes</th>
<th>Basket</th>
";
echo "<tr>";

$ecost=0.0; $unitquan=0.0; $totfreight=0.0; $rowtot=0.0;
$previnvno = "fnord";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
//	echo "<td width=300>". $row['title'] ."</td>";
	if ($row['biblionumber'])
		echo '<td width=200><b><a href="'.$addrkoha433 . $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a></b></td>';	
	else
		echo '<td width=200><b>'. $row['title'].'</b></td>';
//	echo "<td width=100>". $row['booksellerinvoicenumber'] ."</td>";
	echo "<td><b><a href='aqinvoice.php?invoice=". $row['booksellerinvoicenumber'] ."'>"
		. $row['booksellerinvoicenumber'] ."</a></td>";
	echo "<td>". $row['datereceived'] ."</td>";
	echo "<td align=right> $ ". number_format($row['unitprice'],2) ."</td>";
	echo "<td align=right>". $row['quantityreceived'] ."</td>";
	echo "<td align=right><b>". number_format( $row['unitprice'] * $row['quantityreceived'],2) ."</b></td>";
	$truefreight = 0;
	if ($previnvno != $row['booksellerinvoicenumber']) $truefreight=$row['freight'];
	echo "<td align=right> $ ". number_format($truefreight,2) ."</td>";
	echo "<td width=200>". $row['notes'] ."</td>";
//	echo "<td>". $row['basketno'] ."</td>";
	echo '<td><b><a href="'.$addrkoha433 . $aqui_basket_link . $row['basketno'] .'">'.$row['basketno'].'</a></b></td>';	
/*	echo "<td><b>  ". $row['bookfundid'] ." </b></td>";
	echo "<td>   $ ". number_format($row['sum(rrp * quantityreceived)'],2) ." </td>";
	echo "<td>   $ ". number_format($row['sum(ecost * quantityreceived)'],2) ." </td>";
	echo "<td>   $ ". number_format($row['sum(unitprice*quantityreceived)'],2) ." </td>";
*/	
	$ecost += $row['unitprice'];
	$unitquan += $row['quantityreceived'];
	$totfreight += $truefreight;
	$rowtot += $row['unitprice'] * $row['quantityreceived'];
	echo "</tr>";
	$previnvno = $row['booksellerinvoicenumber'];
	}
echo "<tr align=right><td></td><td></td><td></td>
	  <td> $ ".number_format($ecost,2)."</td>
	  <td>".$unitquan."</td> 
	  <td><b> $ ".number_format($rowtot,2)."</b></td> 
	  <td> $ ".number_format($totfreight,2)."</td>
	  <td><b> Grand Total Spent = $ ".  number_format( $rowtot + $totfreight ,2) . "<br /><hr /><i>breakdown</i><br /><hr />
	  		Budget = $ ". number_format( $budget ,2) . "<br />
			- Spent = $ " . number_format( $rowtot ,2) . "<br />
	        - Freight(prv) = $ " . number_format( $totfreight ,2) . "<br /><hr />
	  		<i>Remaining Budget</i> = $ ".  number_format( $budget - $rowtot - $totfreight ,2) ."</b></td><td></td>
	  ";
echo "</table>";

// close database
disconnect($conn);
?>
