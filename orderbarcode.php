<?php
## Order and temp barcodes

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

// Load the link prefix set for all koha-extras
require_once('addrkoha.php');			
// Print the main top menu
require_once("topmenu.php");

// Change these as needed
$prefix1 = "order";
$prefix2 = "temp";
$prefix3 = "history";

echo '  <h3 align="center">Temporary Barcodes</h3>
	<p align="center">[C]opy then [P]aste the barcode below </p>
	<hr>
	<p align="center">
	';

echo "<b>For Orders: </b>";
$code = '<input name="message" type="text" value="';
$code .= $prefix1.time();
$code .= '" size="20" maxlength="20">';
echo $code."<br><br><br><hr>";

echo "<center><b>For Temporary Use: </b>";
$code = '<input name="message" type="text" value="';
$code .= $prefix2.time();
$code .= '" size="20" maxlength="20">';
echo $code."</center><br><br>";

echo "<center><b>For Local History: </b>";
$code = '<input name="message" type="text" value="';
$code .= $prefix3.time();
$code .= '" size="20" maxlength="20">';
echo $code."</center><hr>";

echo '</p>
	<p align="center">&nbsp;</p>
	<p align="center">note: these barcodes cannot be scanned, and should be considered for temp use! </p>
	';
?>