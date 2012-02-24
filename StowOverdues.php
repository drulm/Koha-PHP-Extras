<?php
## STOW OVERDUES

## Copyright 2006 Kyle Hall

## Modified 2008 Darrell Ulm

## This file is part of koha-tools.

## koha-tools is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## koha-tools is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with koha-tools; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

/**
  * This report generates lists of overdue items to use for shelfchecking
  * and pdf's of overdue notices to be printed out and mailed to the borrower.
  * This file displays a form to get the settings for the overdues.
  * Note: This one is a bit messy and could use cleaning up, switch to templates.
  * @package koha-tools
  * @subpackage koha-reports
  * @author Kyle Hall
  * @copyright 2006
  */
  
ini_set("memory_limit","256M");
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
// Print the main top menu
require_once("topmenu.php");

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);
?><style type="text/css">
<!--
.style1 {font-weight: bold}
.style2 {font-style: italic}
-->
</style>

<form method="post" action="processOverdues.php" name="prepareNotices" >
  <table height="621" border="0" cellpadding="2" cellspacing="2" style="text-align: left;">
    <tbody>
      <tr>
        <td colspan="2" rowspan="1"><h2 align="center" class="style1"><span style="font-weight: bold;">Overdue
    Reports &amp; Notices
    Generator</span></h2>
         <p align="center" class="style1">Just Press <button value="submit" name="Submit">Submit</button> for Daily Overdues (then CNTL-[P] to Print</p>
                 <div align="center"><span class="style2"><strong>Special Options - </strong>(you will *not* need these in general)</span>            <input type="reset" value="Reset to Defaults" onClick="window.location.reload()" />
        </div>
          </td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>

        <td width="372" height="165">Last name
          <input name="lname" type="text" id="lname" value="%" size="30" maxlength="60">
-- family or single patron
<p>First name
    <input name="fname" type="text" id="fname" value="%" size="30" maxlength="60">
  -- family or single patron </p>
<p> Optional Message on each notice:
    <input name="message" type="text" value="" size="50" maxlength="100">
</p>
<p align="center"><span class="style1">OVERRIDE ONLY, holidays and weekends are calculated automatically!</span></p>
<p align="center"><span class="style1"><strong>
  <input name="holidays" id="holidays" value="0" size="3" maxlength="3">
</strong>Days of Holiday -- days since last notices printed</span></p>
<p>&nbsp;</p></td>

        <td width="418"><p align="center">&nbsp;</p>
          <p><strong>From: </strong><strong>
            <input name="daysAgo1" value= <?php echo $overdue_start; ?> size="8" maxlength="6">
            </strong>Days Ago<strong> To</strong><strong>
            <input name="daysAgo2" value= <?php echo $overdue_end; ?> size="8" maxlength="6">
          </strong> Days Ago</p>
          <p><br>
              <strong>Special Item Rule</strong></p>
          <p> Item Types: <strong>
            <input name="itypelist" id="itypelist" value= <?php echo '"'.$overdue_special_items.'"'; ?> size="50" maxlength="100">
          </strong></p>
          <p>Days Ago: <strong>
            <input name="specialdaysago" id="specialdaysago" value= <?php echo $overdue_special_daysago; ?> size="8" maxlength="6">
</strong></p>
          <p><em>Include Users w/ Email Account for Printed Notices:
            </em>
            <input name="email" type="checkbox" id="email" value="email">
          </p></td>

      </tr>

      <tr>

        <td height="176"><br>
        <h3><strong><strong>Report Type:</strong></strong></h3>
        <input name="reportType" type="radio" value="mail" checked>
Notices to Mail Out
<p>
  <input name="reportType" value="shelf" type="radio">
  Notices to Check Shelves</p>
<p>
  <input name="reportType" value="email" type="radio">
  Email Notices </p>

        </td>

        <td><p>&nbsp;</p>
        <p>&nbsp;        </p>
        <table width="100%" height="18%" border="1">
          <tr>
            <td><p><strong>Include Patron Types:</strong>
            </p>
              <p>
			 <select name="patrons[]" multiple >
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
              </p></td>
            <td><p><strong>Include Library Branches: </strong>                 
            </p>
              <p>
                <select name="library">
					 <?php
					$values2 = array(); $lib2= array();
					get_branches($conn, $values2, $lib2);
					$i = 0;
					foreach ($values2 as $brcode)
						{
						echo '<option selected="selected" value="'.$brcode.'">'.$lib2[$i].'</option>';
						$i++;
						}
					?>
                </select>
					<?php
					// close database
					disconnect($conn);
					?>
              </p></td>
          </tr>
        </table>        <p>          <strong>          </strong></p></td>

      </tr>

    </tbody>
  </table>

  <strong></strong>
</form>
