<?php



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <link href="../styles.css" rel="stylesheet" type="text/css" />
  <style type="text/css">
<!--
.style1 {color: #000099}
.style2 {color: #990000}
-->
  </style>
</head>


<body onload="document.prepareNotices.Submit.focus()" >

<strong><strong></strong></strong><strong></strong>
<form method="post" action="uniquecheck.php" name="prepareNotices" >
  <table style="text-align: left;" border="0" cellpadding="2" cellspacing="2">
    <tbody>
      <tr>
        <td colspan="2" rowspan="1">
		<?php
echo '
<div align="center">
  <h2 align="left" class="style8"><a href="koha_rep.htm"> </a><a href="'.$addrkoha433.'">Koha Intranet</a> | <a href="'.$addrkoha433.'cgi-bin/koha/reports-home.pl">Reports</a> </h2>
  ';
  ?>
		
        <h2 align="center" class="style1">Check colections to add Unique-mgmt $10 charge </h2>
        <p align="center" class="style1"><p align="center" class="style1">Press 
		
		<button value="Submit" name="Submit">Submit</button>
		
		</p>
		 </p>
        <p align="center"> Last name
            <input name="lname" type="text" id="lname" size="30" maxlength="60">
  </p>
        <p align="center">First name
            <input name="fname" type="text" id="fname" size="30" maxlength="60">
</p>
        <p align="center">Name required to find records(s)</p>
        <hr>
        <p align="center" class="style1"><span class="style2"><strong>Special Options - </strong>(you will *not* need these in general)</span>            
          <input type="reset" value="Reset to Defaults" style="background-color:#881111; color: #ffffff;" onClick="window.location.reload()" />
        </p>
          <hr>
        <p align="center"><span class="style1">OVERRIDE ONLY, holidays and weekends are calculated automatically!</span></p>
        <p align="center"><span class="style1"><strong>
          <input name="holidays" id="holidays" value="0" size="3" maxlength="3">
        </strong>Days of Holiday -- days since last notices printed </span></p>
        </td>
      </tr>

      <tr>

        <td width="454">
		Library: <?php $_REQUEST['library'];?>
		<input type="hidden" name='library' value='<?php $_REQUEST['library'];?>'>

        </td>

        <td width="167">&nbsp;
		
		</td>

      </tr>

      <tr>

        <td>
        <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">

          <tbody>

            <tr>

              <td><strong>Dollar Cutoff</strong> <strong>
                  <input name="dollarcutoff" class="even" id="dollarcutoff" value="25.00" size="10" maxlength="10">
              </strong></td>

              </tr>

            <tr valign="top">

              <td><p><strong>Include Patron Types:</strong></p>
                <input checked="checked" name="PA" value="PA" type="checkbox">
                Adult Patron<br>
                <input name="YV" type="checkbox" value="YV" checked>
                5-17 Yes Video<br>
                <input name="NV" type="checkbox" value="NV" checked>
                5-17 No Video<br>
                <input name="TE" value="TE" type="checkbox">
                Teacher<br>
                <input name="ST" value="ST" type="checkbox">
                Staff + Board<br>
                <input name="IL" type="checkbox" value="IL">
Inter Library Loan<br>
<input name="OR" value="OR" type="checkbox">
Outreach
<p>&nbsp;</p></td>

            </tr>

          </tbody>
        </table>

        <br>

        </td>

        <td><span style="font-weight: bold;"></span>
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

        <p><span style="font-weight: bold;"></span><br>
          <strong>          </strong></p>
        </td>

      </tr>

    </tbody>
  </table>

  <strong></strong>
</form>

</body>
</html>
