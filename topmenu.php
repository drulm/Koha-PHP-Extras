<?php
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

// Show the normal menu
// Set this to your Koha staff intranet base address

require_once("addrkoha.php");

if ($koha_intranet_css != "")
	echo'<style type="text/css"> 
		@import url('.$addrkoha433.$koha_intranet_css.' );
	</style>';
	
if ($koha_intranet_colors != "")
	echo'<style type="text/css"> 
		@import url('.$addrkoha433.$koha_intranet_colors.');
	</style>';

/*echo '<h2 align="left"><a href="'.$addrkoha433.'">Koha Intranet</a> | <a href="'.
	$addrkoha433. $reportpage . '">Koha Reports Page</a> | 
	<a href="reports.php">KohaExtras</a> </h2>';
*/

echo '<h2 align="left"><a href="'.$addrkoha433.'">Koha Intranet</a></h2>';

?>
