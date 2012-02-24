<?php
## Phone List 

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

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <link href="../styles.css" rel="stylesheet" type="text/css" />
<style type="text/css"> 
	@import url(http://66.213.124.205/intranet-tmpl/npl/en/includes/intranet.css);
	@import url(http://66.213.124.205/intranet-tmpl/npl/en/includes/colors.css);
	
</style>
<link rel="stylesheet" media="print" href="/intranet-tmpl/npl/en/includes/print-receipt.css" type="text/css">
<style type="text/css">
<!--
.style1 {font-style: italic}
.style2 {font-style: italic}
-->
</style>
</head>

<body onload="document.prepareNotices.Submit.focus()" >

<form method="post" action="processPhoneList.php" name="prepareNotices" >
  <table style="text-align: left;" border="0" cellpadding="2" cellspacing="2">
    <tbody>
      <tr>
        <td colspan="2" rowspan="1">
        <h2 align="center" class="style1"><span style="font-weight: bold;"> CALL - Phone for Reserve List </span></h2>
        <p align="center" class="style1">Just Press 
		
		<button value="submit" name="Submit">Submit</button>
		</p>
        <hr>
        <div align="center">
          <p><span class="style2"><strong>Special Options - </strong>(you will *not* need these in general)</span>              <input type="reset" value="Reset to Defaults" onClick="window.location.reload()" />          
          </p>
          </div>
        <p align="center">OVERRIDE ONLY, holidays and weekends are calculated automatically!        
        <p align="center"><strong>
          <input name="holidays" id="holidays" value="0" size="3" maxlength="3">
        Days of Holiday</strong> -- days since last notices printed</p>
        <p align="center">
          <input name="email" type="checkbox" id="email" value="email">
          <em><strong>Email </strong>-Include patrons with email accounts lists on patron record </em></p></td>
      </tr>
      <tr>
        <td width="454">
		Library:  <?php echo $_REQUEST['library']; ?>
		<input type="hidden" name='library' value=' <?php echo $_REQUEST['library']; ?> '>
        </td>
        <td width="167">&nbsp;
		</td>
      </tr>
      <tr>
        <td height="213"><h3><strong><strong>Report Type:</strong></strong></h3>
          <p><strong>Include Patron Types: </strong>Hold CTRL to select multiples </p>
          <select name="collection[]" MULTIPLE >
            <option selected="selected" value="%">*ALL* CODES</option>
            <?php
		$values = array(); $lib= array();
		get_patron_types($conn, $values, $lib);
		$i = 0;
		foreach ($values as $patcode)
			{
			echo '<option selected="selected" value="'.$patcode.'">'.$lib[$i].'</option>';
			$i++;
			}
		?>
          </select>
          <br>
        </td>
        <td><span style="font-weight: bold;"></span>
          <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
            <tbody>
              <tr>
                <td><h3><strong>From:</strong></h3>
                    <strong> </strong></td>
              </tr>
              <tr>
                <td><strong>
                  <input name="daysAgo1" value="0" size="3" maxlength="3">
                </strong>Days Ago</td>
              </tr>
              <tr>
                <td><strong>To</strong></td>
              </tr>
              <tr>
                <td><strong>
                  <input name="daysAgo2" value="0" size="3" maxlength="3">
                </strong> Days Ago</td>
              </tr>
              <tr>
                <td height="32"><strong>Date Range </strong></td>
              </tr>
            </tbody>
          </table>
          <p><span style="font-weight: bold;"></span><br>
          <strong>          </strong></p>
        </td>
      </tr>
    </tbody>
  </table>

  <strong></strong>

</form>

<?php
// close database
disconnect($conn);
?>
