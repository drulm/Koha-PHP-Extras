

rem if (sizeof($argv)>1)
rem	{
rem	$library		= $argv[1];
rem	$noticeNumber		= $argv[2];
rem	$message		= $argv[3];
rem	$lname			= $argv[4];
rem	$fname			= $argv[5];
rem	$holidays		= $argv[6]; 
rem	$itypelist		= $argv[7];
rem	$specialdaysago		= $argv[8];
rem	$daysAgo1		= $argv[9];
rem	$daysAgo2		= $argv[10];
rem	$reporttype		= $argv[11];
rem	}


rem 	Library: MAIN
rem 	noticeNumber
rem 	lname %
rem 	fname %
rem 	holidays 0
rem 	itypelist DVD NDVD NFDV NNDV
rem 	specialdays ago 6
rem 	daysAgo1 13
rem 	daysAgo2 13
rem 	reporttype shelf 

"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\KohaTools\overdueNotices\processOverdues.php" "MAIN" 0 "a pre-overdue" "" "" 0 "DVD NDVD NFDV NNDV" 0 0 0 "email" "PA YV NV TE ST OR" 


