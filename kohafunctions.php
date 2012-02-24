<?php
## Copyright 2008 Darrell Ulm

## kohafunctions.php is many of the libraries you rest of the code uses. 

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

//Database Abstraction
require_once "DB.php";
function handlePearError($error) {
	echo "$error->message<br />n",
		$error->userinfo;
	exit;
}
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handlePearError');
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');

// Open the database
function open_database($db_type, $koha_login, $koha_password, $koha_ip, $koha_name)
{
$dsn = "$db_type://$koha_login:$koha_password@$koha_ip/$koha_name";
$conn = DB::connect ($dsn);
if (DB::isError ($conn))
die ("Cannot connect: " . $conn->getMessage () . "\n");
return $conn;
}

function read_db_assoc($result) { return $result->fetchRow (DB_FETCHMODE_ASSOC); }

function numrows($result) { return $result->numRows(); }

function numcols($result) { return $result->numCols(); }

function field_names($result) { return $result->tableInfo(); }

function disconnect($conn){ $conn->disconnect(); }

function dbquery($conn, $query)
{
$result = $conn->query($query);
if (DB::isError ($result))
die ("QUERY FAILED: " . $result->getMessage () . "<br><b>Entire Query:</b> " . $query );
return $result;
}

//---------Koha Specific--------------------------------------------------------------
// Build the Query
function build_koha_search($title, $author, $series, $abstract, $isbn)
{
$query = "select *
    from biblio, biblioitems 
	where biblio.biblionumber = biblioitems.biblionumber ";
if ($title != NULL) $query .= " and title LIKE '%".$title."%' ";
if ($author != NULL) $query .= " and author LIKE '%".$author."%' ";
if ($series != NULL) $query .= " and seriestitle LIKE '%".$series."%' ";
if ($abstract != NULL) $query .= " and abstract LIKE '%".$abstract."%' ";
if ($isbn != NULL) $query .= " and biblioitems.isbn LIKE '%".$isbn."%' ";
$query .= " order by biblio.datecreated DESC "; 
$query .= " limit 0,100 " ;
//echo $query;
return $query;
}

function get_authorised_values($conn, $category, & $values, & $lib)
{
$query = "select * from authorised_values where category = '$category' order by authorised_value";
$result = dbquery($conn, $query);
$values = array();
$lib = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	array_push($values, $row['authorised_value']);
	array_push($lib, $row['lib']);
	}
}

function get_patron_types($conn, & $values, & $lib)
{
$query = "select categorycode, description from categories";
$result = dbquery($conn, $query);
$values = array();
$lib = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	array_push($values, $row['categorycode']);
	array_push($lib, $row['description']);
	}
}

function get_branches($conn, & $values, & $lib)
{
$query = "select branchcode, branchname from branches";
$result = dbquery($conn, $query);
$values = array();
$lib = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	array_push($values, $row['branchcode']);
	array_push($lib, $row['branchname']);
	}
}

function build_patron_categories($collection)
{
$sql = "";
if ($collection[0] != "%" && count($collection)!=0)
	{
	$sql .= " and ";
	$sql .=  "  ( ";
	$i = 0;
	while ($i < count($collection))
		{
		$sql .= " borrowers.categorycode = ".' "'.stripslashes($collection[$i]).'" '." ";
		$i++;
		if ($i < count($collection)) $sql .= " OR ";
		}
	$sql .=  " ) ";
	}
return $sql;
}

//--- should combine this later with build_patroncategories by adding parameter for the comparison
function build_items($collection)
{
$sql = "";
if ($collection[0] != "%" && count($collection)!=0)
	{
	$sql .= " and ";
	$sql .=  "  ( ";
	$i = 0;
	while ($i < count($collection))
		{
		$sql .= " items.itype = ".' "'.stripslashes($collection[$i]).'" '." ";
		$i++;
		if ($i < count($collection)) $sql .= " OR ";
		}
	$sql .=  " ) ";
	}
return $sql;
}

function get_holidays($holidays, $local_holiday, $week, $koha_db, $local_login, $local_password, $local_ip, $local_name)
{
if ($local_holiday)
	{
	$dayofweek = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")));
	// Open the "local" database
	$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);
	
	$query = "select * from holiday
	where holdate = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 2 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 4 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 5 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
	
	// Perform Query
	$result = dbquery($conn, $query);
	$num_rows = numrows($result);
	
	while ($row = read_db_assoc($result))
		{
		$hrow = stripslashes_deep($row);
		$harr = stripslashes($hrow['holdate']) ;
		$j = jddayofweek ( cal_to_jd(CAL_GREGORIAN, (int)substr($harr,5,2), (int)substr($harr,8,2), (int)substr($harr,0,4)));
		$week[$j] = 1;
		}

	//count backwards til we hit a 0 (non holiday) modulo 7
	$cd = $dayofweek + 7 - 1;	// start + 7 for % and - 1 for prev day
	$hols = 0;					// zero holidays so far
	while ($week[$cd%7] != 0 && $hols<7)
		{
		$cd = $cd - 1;
		$hols++;
		}
	if ($holidays == 0)				// check for override --- usually 0
		$holidays = $hols; 			// works, lets set it
	// close database
	disconnect($conn);
	}
return $holidays;
}

// Works a little differently, will count number of holidays in prior week total. 
function any_holidays($holidays, $local_holiday, $week, $koha_db, $local_login, $local_password, $local_ip, $local_name)
{
if ($local_holiday)
	{
	$dayofweek = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")));
	// Open the "local" database
	$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);
	
	$query = "select * from holiday
	where holdate = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 2 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 4 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 5 DAY)
	or holdate = DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
	
	// Perform Query
	$result = dbquery($conn, $query);
	$num_rows = numrows($result);
	
	while ($row = read_db_assoc($result))
		{
		$hrow = stripslashes_deep($row);
		$harr = stripslashes($hrow['holdate']) ;
		$j = jddayofweek ( cal_to_jd(CAL_GREGORIAN, (int)substr($harr,5,2), (int)substr($harr,8,2), (int)substr($harr,0,4)));
		$week[$j] = 1;
		}

	//count backwards til we hit a 0 (non holiday) modulo 7
	$cd = $dayofweek + 7 - 1;	// start + 7 for % and - 1 for prev day
	$hols = 0;					// zero holidays so fara
	$cnt = 0;
	while ($cnt<7 && $hols<7)
		{
		if ( $week[$cd%7] != 0 ) $hols++;
		$cd --;
		$cnt ++;
		}
	if ($holidays == 0)				// check for override --- usually 0
		$holidays = $hols; 			// works, lets set it
	// close database
	disconnect($conn);
	}
return $holidays;
}

// The where clause for get_koha_stat
function where_clause($query)
{
return strcmp(trim(strtolower(substr($query, strrpos($query, "where"), strlen($query)))) , "where" );
}

// Get stat number from MySQL
function get_koha_stat($conn, $query, $startdate, $enddate, $datefield)
{ 
// Add in date stuff to query
if ($datefield)
	{
	//echo "<hr>".trim(strtolower(substr($query, strrpos($query, "where"), strlen($query)))). "<hr>";
	if ( where_clause($query) ) $query .= " and ";
	if ($startdate != "") $query .= " ".$datefield." >= ".'"'.$startdate.'"'."  ";
	if ( where_clause($query) ) $query .= " and ";
	if ($enddate != "") $query .= " ".$datefield." <= ".'"'.$enddate.'"'."  ";
	}
// Perform Query
$result = dbquery($conn, $query);
$row = read_db_assoc($result);
$row = stripslashes_deep($row);
return($row);
}

function GetBudgetsByDates($conn, $start, $end, & $bookfundid, & $budgetamount, & $budgetid, & $freights )
{
$startdate = explode(" ", $start);
$enddate = explode(" ", $end);
$query = "select sum(budgetamount), bookfundid, aqbudgetid from aqbudget 
		where 
		startdate <= '". $enddate[0] ."'
		and enddate >= '". $enddate[0] ."'
		group by bookfundid";
$result = dbquery($conn, $query);
$bookfundid = array();
$budgetamount = array();
$budgetid = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	array_push($bookfundid, $row['bookfundid']);
	array_push($budgetamount, $row['sum(budgetamount)']);
	array_push($budgetid, $row['aqbudgetid']);
	}
	
$freights = array();
$query = "SELECT 
		MAX(freight), bookfundid
		FROM 
		aqorders
		LEFT JOIN aqorderbreakdown ON (aqorderbreakdown.ordernumber = aqorders.ordernumber)
		WHERE 
		datereceived >= '". $startdate[0] ."'
		AND 
		datereceived <= '". $enddate[0] ."'
		GROUP BY 
		booksellerinvoicenumber";
$result = dbquery($conn, $query);
$freights = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$key = array_search($row['bookfundid'], $bookfundid);
	$freights[$key] += $row['MAX(freight)'];
	}
}

function GetFreightCost($conn, $start, $end )
{
$startdate = explode(" ", $start);
$enddate = explode(" ", $end);
$query = "
		SELECT max(freight) FROM 
		aqorders
		WHERE
		datereceived >= '". $startdate[0] ."'
		AND 
		datereceived <= '". $enddate[0] ."'
		GROUP BY booksellerinvoicenumber
		";
$result = dbquery($conn, $query);
$fsum=0.0;
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$fsum += $row['max(freight)'];
	}
return $fsum;
}


//----------MYSQL ONLY WRAPPERS--------------------------------------------------------
// connect to the web site 
function open_my_sql_database($koha_login, $koha_password, $koha_ip, $koha_name)
{
$link = mysql_connect($koha_ip, $koha_login, $koha_password);
if (!$link) die('<br>Could not connect to database, please try again later.<br>' . mysql_error());
$db = mysql_select_db($koha_name, $link);
if (!$db) die ('<br>Can\'t use koha database.<br>' . mysql_error());
return $db;
}

function query_mysql($query)
{
$result = mysql_query($query);
if (!$result) 
	{
   $message  = 'Invalid query: ' . mysql_error() . "\n";
   $message .= 'Whole query: ' . $query;
   die($message);
	}
return $result;
}

//-------------Misc Functions---------------------------------------------------------
function stripslashes_deep($value) 
{
	if (is_array($value)) {
		if (count($value)>0) {
			$return = array_combine(array_map('stripslashes_deep', array_keys($value)),array_map('stripslashes_deep', array_values($value)));
		} else {
			$return = array_map('stripslashes_deep', $value);
		}
		return $return;
	} else {
		$return = stripslashes($value);
		return $return ;
	}
}

function sortByField($multArray,$sortField,$desc=true){
           $tmpKey='';
           $ResArray=array();

           $maIndex=array_keys($multArray);
           $maSize=count($multArray)-1;

           for($i=0; $i < $maSize ; $i++) {

               $minElement=$i;
               $tempMin=$multArray[$maIndex[$i]][$sortField];
               $tmpKey=$maIndex[$i];

               for($j=$i+1; $j <= $maSize; $j++)
                 if($multArray[$maIndex[$j]][$sortField] < $tempMin ) {
                     $minElement=$j;
                     $tmpKey=$maIndex[$j];
                     $tempMin=$multArray[$maIndex[$j]][$sortField];

                 }
                 $maIndex[$minElement]=$maIndex[$i];
                 $maIndex[$i]=$tmpKey;
           }

           if($desc)
               for($j=0;$j<=$maSize;$j++)
                 $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];
           else
             for($j=$maSize;$j>=0;$j--)
                 $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];

           return $ResArray;
       }
	  
//----------------------------------------------------------------------------------
function refocus($fform, $ffield)  // javascript to focus the cursor in a field
{
 echo '
<script type="text/javascript">
document.'.$fform.'.'.$ffield.'.focus();
</script>
';
}
?>
