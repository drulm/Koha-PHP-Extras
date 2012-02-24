<?php
## Koha Unique Management Submit

## Modified by Darrell Ulm 2008

## Built from:
## KOHA OVERDUES

## Copyright 2006 Kyle Hall

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
?>


<strong><strong></strong></strong><strong></strong>
<form method="post" action="unique.php" name="prepareNotices" >
  <table border="0" align="center" cellpadding="2" cellspacing="2" style="text-align: left;">
    <tbody>
      <tr>
        <td colspan="2" rowspan="1">
		
        <h2 align="center" class="style1">Collections FTP to Unique Management</h2>
        <p align="center" class="style1">Just Press 
		
		<button value="Submit" name="Submit">Submit</button>
		
		for the Fees list or the FTP Report </p>
        <hr>
<div align="center"><strong><strong>Report Type:</strong></strong></div>
<table width="100%"  border="1">
  <tr>
    <td width="49%"><div align="right">
      <input name="reportType" type="radio" value="check" checked>
Unique Check Fees List 
      </div></td>
    <td width="51%"><div align="left">
      <input name="reportType" type="radio" value="shelf">
FTP Report</div></td>
  </tr>
</table>
<p align="center" class="style1"><span class="style2"><strong>Special Options - </strong>(you will *not* need these in general)</span>            
            <input type="reset" value="Reset to Defaults" onClick="window.location.reload()" />
        </p>
<p align="center">Last name
            <input name="lname" type="text" id="lname" value="%" size="30" maxlength="60">
  -- family or single patron </p>
        <p align="center">First name
            <input name="fname" type="text" id="fname" value="%" size="30" maxlength="60">
  -- family or single patron </p>
                <p align="center"><span class="style1">OVERRIDE ONLY, holidays and weekends are calculated automatically!</span></p>
        <p align="center"><span class="style1"><strong>
          <input name="holidays" id="holidays" value="0" size="3" maxlength="3">
        </strong>Days of Holiday -- days since last notices printed </span></p>
        </td>
      </tr>

      <tr>

        <td width="454" height="204">
        <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">

          <tbody>

            <tr>

              <td width="55%" rowspan="2"><h3><strong>Include Patron Types:</strong></h3>
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
                <p></p></td>

              <td width="45%">                Dollar Cutoff <strong>
                  <input name="dollarcutoff" class="even" id="dollarcutoff" value="25.00" size="10" maxlength="10">
              </strong></td>

            </tr>

            <tr valign="top">

<!--              <td height="82">Select Branch 
			         </strong></p>
                <p>
                  <select name="library">
				      <?php
/*					$values2 = array(); $lib2= array();
					get_branches($conn, $values2, $lib2);
					$i = 0;
					foreach ($values2 as $brcode)
						{
						echo '<option selected="selected" value="'.$brcode.'">'.$lib2[$i].'</option>';
						$i++;
						}
*/					?>
                  </select></td>
-->
            </tr>

          </tbody>
        </table>

        <br>

        </td>

        <td width="167"><span style="font-weight: bold;"></span>
        <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">

          <tbody>

            <tr>

              <td>
              <h3><strong>From:</strong></h3>

              <strong> </strong></td>

            </tr>

            <tr>

              <td><strong> <input name="daysAgo1" value="65" size="6" maxlength="6"> 
              </strong>Days Ago</td>

            </tr>

            <tr>

              <td><strong>To</strong></td>

            </tr>

            <tr>
              <td><strong><input name="daysAgo2" value="72" size="6" maxlength="6">
              </strong>
Days Ago</td>
            </tr>
            <tr>
              <td height="32"><strong>Date Range 
                </strong></td>
            </tr>
          </tbody>
        </table>

        <p><br>
          <strong>          </strong></p>
        </td>

      </tr>

    </tbody>
  </table>

</form>

</body>
</html>
