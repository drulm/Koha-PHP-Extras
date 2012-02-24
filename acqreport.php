<?php
## Acqui. Budgets By Patron ID 

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
$startdate		= $_POST['startdate']." 00:00:00";
$enddate		= $_POST['enddate']." 23:59:59";

echo "<h3>Date Range: ( $startdate )  to  ($enddate)  </h3>";

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$bookfundid = array();
$budgetamount = array();
$budgetid = array();
$freights = array();
GetBudgetsByDates($conn, $startdate, $enddate, $bookfundid, $budgetamount, $budgetid, $freights );
$fcost = GetFreightCost($conn, $startdate, $enddate );

$query = "select sum(rrp * quantityreceived), sum(ecost * quantityreceived) ,
	sum(unitprice*quantityreceived), aqorderbreakdown.bookfundid, 
	aqorders.basketno,   aqorders.biblionumber, 
	aqorders.ordernumber, aqorders.title, datereceived, 
	GROUP_CONCAT(title order by aqorders.ordernumber ASC SEPARATOR '<br>') as alltitle
	# aqbooksellers.id,
		from
	aqorders, aqorderbreakdown
	# ,aqbasket,aqbooksellers,aqbudget, name, 
		where
	# aqorders.biblioitemnumber = biblioitems.biblioitemnumber and
	# aqorders.basketno = aqbasket.basketno and
	# aqbasket.booksellerid = aqbooksellers.id and
	aqorders.ordernumber = aqorderbreakdown.ordernumber and
	# aqorderbreakdown.bookfundid = aqbudget.bookfundid and
	quantityreceived IS NOT NULL
	and (datecancellationprinted IS NULL
	or datecancellationprinted='0000-00-00')
	and datereceived IS NOT NULL 
	# and rrp < 100000
	 ";
	
if ( where_clause($query) ) $query .= " and ";
if ($startdate != "") $query .= " datereceived >= ".'"'.$startdate.'"'."  ";
if ( where_clause($query) ) $query .= " and ";
if ($enddate != "") $query .= " datereceived <= ".'"'.$enddate.'"'."  ";
	
//	and datereceived >= '2007-02-25'
//	and datereceived <= '2008-02-25'

$query .=	" group by aqorderbreakdown.bookfundid";

echo "<h2>Budget by Bookfund</h2>";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr align=right> 
	<th>BookFund</th>
	<th>Budget</th>
	<th>Sum of RPP</th>
	<th>Sum of Est. Cost</th>
	<th>Spent<br /><i>prior freight not added in</i><br />Click below for calculation<br />with prior freight method</th>
	<th>Freight Mthd.<br />Depreciated<br />Prior to 5/2009</th>
	<th>Remaining Budget<br /><i>prior freight not subtracted</i></th>
	<th>Remaining Budget<br /><i>prior freight IS subtracted</i></th>";
//	<th>Titles</th>";
echo "<tr>";

$rpp=0.0; $ecost=0.0; $unitquan=0.0; $budgettotal=0.0; $remainingtotal=0.0; $frtot=0.0; $frtot2=0.0;
while ($row = read_db_assoc($result))
	{
//	echo $row['bookfundid'];
	$row = stripslashes_deep($row);
	echo "<tr align=right>";
	$key = array_search($row['bookfundid'], $bookfundid);
//	echo $budgetid[$key] ;
	echo "<td><b>  <a href='". $addrkoha433 . $aqui_budget_link. $budgetid[$key]. "'>". $row['bookfundid'] ."</a></b></td>";
	echo "<td><b>  ". $budgetamount[$key]  ." </b></td>";
	echo "<td>   $ ". number_format($row['sum(rrp * quantityreceived)'],2) ." </td>";
	echo "<td>   $ ". number_format($row['sum(ecost * quantityreceived)'],2) ." </td>";
#	echo "<td><b>   $ ". number_format($row['sum(unitprice*quantityreceived)'],2) ." </b></td>";
	echo "<td><b>   $ <a href='acqreportitems.php?startdate=". $startdate 
		."&enddate=". $enddate 
		."&bookfundid=". $row['bookfundid'] 
		."&budget=". $budgetamount[$key]
		."'>". number_format($row['sum(unitprice*quantityreceived)'],2) ."</a></td>";
	echo "<td>". number_format($freights[$key],2) ."</td>";
	$rpp += $row['sum(rrp * quantityreceived)'];
	$ecost += $row['sum(ecost * quantityreceived)'];
	$unitquan += $row['sum(unitprice*quantityreceived)'];
	$budgettotal += $budgetamount[$key];
	$budgetremaining = $budgetamount[$key] - $row['sum(unitprice*quantityreceived)'] - $row['sum(freight)'];
	$remainingtotal += $budgetremaining;
	$frtot2 += $freights[$key];
	echo "<td><b>   $ ". number_format($budgetremaining ,2) ." </b></td>";
	echo "<td><b>   $ ". number_format($budgetremaining-$freights[$key] ,2) ." </b></td>";
//	echo "<td> " . $row['alltitle'] . "</td>";
	echo "</tr>";
	}
$spentnfreight = $unitquan + $frtot;
echo "<tr></tr><tr align=right>
	<td> <b>TOTALS</b> </td>
	<td> <b>$". number_format($budgettotal,2) ."</b></td> 
	<td> $ ". number_format($rpp,2) ."</td>  
	<td> $ ". number_format($ecost,2) ."</td>
	<td> <b>$ ". number_format($unitquan,2) ."</b></td>
	<td> <b>$ ". number_format($frtot2,2) ."</b></td>
	<td> <b>$ ". number_format($remainingtotal,2) ."</b></td>";
echo "</tr>";
echo "<tr></tr><tr align=right><td></td><td></td><td></td><td></td>
	<td> <b><i>+ Freight<br />Depreciated<br />(from prior method):<br />before May 2009<br />$ ". number_format($frtot2,2) ."</b></td> 
	<td> <b>Grand Total:<br />$ ". number_format($unitquan + $fcost,2) ."</i></b></td><td></td>
	<td> <b>Remaining Budget<br />With Prior Freight Mthd.<br />(before May 2009)<br />$ ". number_format($remainingtotal-$frtot2,2) ."</i></b></td>
	";
echo "</tr></table><br><br><hr>";

echo "<h2> Budget by Vendor Totals </h2>";

$query = 
	"select sum(rrp * quantityreceived), sum(ecost * quantityreceived) ,
	sum(unitprice * quantityreceived), sum(aqorders.listprice * quantityreceived) , 
	aqorders.basketno, aqbooksellers.id,  aqorders.biblionumber, 
	aqorders.ordernumber,name,title 
		from
	aqorders,aqbasket,aqbooksellers 
		where
	# aqorders.biblioitemnumber=biblioitems.biblioitemnumber and
	aqorders.basketno = aqbasket.basketno
	and aqbasket.booksellerid = aqbooksellers.id
	and quantityreceived IS NOT NULL
	and (datecancellationprinted IS NULL
	or datecancellationprinted='0000-00-00')
	and datereceived IS NOT NULL 
	# and rrp < 100000
	";
	
if ( where_clause($query) ) $query .= " and ";
if ($startdate != "") $query .= " datereceived >= ".'"'.$startdate.'"'."  ";
if ( where_clause($query) ) $query .= " and ";
if ($enddate != "") $query .= " datereceived <= ".'"'.$enddate.'"'."  ";
	
//	and datereceived >= '2007-02-25'
//	and datereceived <= '2008-02-25'
	
$query .=	"group by name";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr align=right> 
<th>Vendor</th>
<th>Sum of RPP</th>
<th>Sum of Est. Cost</th>
<th>Sum of List Price</th>
<th>Sum of Unit Price<br />Not including<br />Freight Prior to<br />May 2009</th>
<th>Vend Id</th>
<tr>";

$rpp=0.0; $ecost=0.0; $unitquan=0.0; $list=0.0; 
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr align=right>";
	echo "<td><b> ". $row['name'] ." </b></td>";
	echo "<td>$ ". number_format($row['sum(rrp * quantityreceived)'],2) ." </td>";
	echo "<td>$ ". number_format($row['sum(ecost * quantityreceived)'],2) ." </td>";
	echo "<td>$ ". number_format($row['sum(aqorders.listprice * quantityreceived)'],2) ." </td>";
	echo "<td><b>$ ". number_format($row['sum(unitprice * quantityreceived)'],2) ." </b></td>";
	echo "<td><b>  <a href='". $addrkoha433 . $aqui_receive_link. $row['id'] . "'>". $row['id']  ."</a></b></td>";
//	echo "<td>  ". $row['id'] ." </td>";
	$rpp += $row['sum(rrp * quantityreceived)'];
	$ecost += $row['sum(ecost * quantityreceived)'];
	$unitquan += $row['sum(unitprice * quantityreceived)'];
	$list += $row['sum(aqorders.listprice * quantityreceived)'];
	echo "</tr>";
	}
echo "<tr>";
echo "<tr align=right>
		<td><b>Totals: </b></td>
		<td><b> $ ".number_format($rpp,2) ."</b></td>
		<td><b> $ ".number_format($ecost,2) ."</b></td>
		<td><b> $ ".number_format($list,2) ."</b></td>
		<td><b> $ ".number_format($unitquan,2) ."</b></td>
		<td></td></tr>
		";

echo "<tr></tr><tr align=right>
	<td></td>
	<td> <b><i>+ Freight<br />Depreciated<br />(from prior method):<br />before May 2009<br />$ ". number_format($fcost,2) ."</b></td> 
	<td> <b>Grand Total:<br />$ ". number_format($unitquan + $fcost,2) ."</i></b></td>
	<td></td><td></td><td></td>";
echo "</tr></table><hr>";
echo "<hr>";

// close database
disconnect($conn);
?>
