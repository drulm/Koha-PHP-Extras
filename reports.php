<!--
## Just a menu of tools

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
-->

<?php
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Print the main top menu
require_once("topmenu.php");
?>

<h2 align="center">Koha Extras Reports (Stow Reports) </h2>
<table border="1" align="center">
  <tr>
    <td width="154"><div align="center"><strong>Circulation</strong></div></td>
    <td width="133"><div align="center"><strong>Reference</strong></div></td>
    <td width="125"><div align="center"><strong>Cataloging</strong></div></td>
    <td width="175"><div align="center"><strong>All</strong></div></td>
  </tr>
  <tr>
    <td>
	<p><a href="processreserveshort.php">Daily Reserve Pull List</a></p>
	<p><a href="cancelres.php">Canceled Reserves </a></p>
	<p><a href="phonelist.php">Phone Call List</a></p>
	<p><a href="circ_stats.php">Circulation Statistics </a></p>
    <p> <?php if ($local_db) echo '<a href="illsubmit.php">Submit ILL </a></p>';
			else echo "Submit ILL(off)</p>" ?>
    <p><a href="patronfind.php">Find Patron By Code </a></p>
    <p><?php if ($local_db) echo '<a href="inhousesubmit.php">In House Count Enter</a></p>';
			else echo "In House Count Enter(off)</p>" ?>
    <p><a href="StowOverdues.php">Over Dues</a></p>
    <p><a href="StowBill.php">Bills</a></p>
    <p><a href="StowUnique.php">Unique Management</a></p>
    <p><a href="StowEmailHold.php">Email Hold Notices </a></p>
    <p><a href="finesfix.php">Patron Bill MaxFines </a></p></td>
    <td>
	<p><a href="holdsalert.php">Hold Alert List </a></p>
    <p><?php if ($local_db) echo '<a href="illsearch.php">ILL Search</a></p>';
			else echo "ILL Search(off)</p>" ?>
    <p><a href="koha_status.php">Status Searches </a></p>
    <p><a href="patronlookuprun.php">Patron Lab Verify</a></p>
    <p><a href="shelf_list.php">Shelf List Reports</a></p>
    <p><a href="holdoverlist.php">Held Over List</a></p>
	</td>
    <td>
	<p><a href="acqfix.php">Acqui. Fix Report </a></p>
    <p><a href="acqrepdate.php">Acqui Budget</a></p>
    <p><a href="acqreportoff.php">Acqui Price Fix </a></p>
    <p><a href="cat_stats.php">Cataloging Stats </a></p>
    <p><a href="orderbarcode.php">Order (Temp) Barcodes</a></p>
    <p><a href="cdreport.php">CD Call Report </a></p>
    <p><?php if ($local_db) echo '<a href="spinesearch.php">Print Spine Lbl</a> </p>';
			else echo "Print Spine Lbl(off)</p>" ?>
	
	</td>
    <td>
	<p><a href="kohabib.php">Alternate SQL Search (when regular search fails) </a></p>
    <p><a href="kohabarcode.php">Barcode Search Only</a></p>
	</td>
  </tr>
</table>

