

rem	$library		= $argv[1];
rem	$noticeNumber		= $argv[2];
rem	$message		= $argv[3];
rem	$lname			= $argv[4];
rem	$fname			= $argv[5];
rem	$dollarcutoff		= $argv[6];
rem	$holidays		= $argv[7]; 
rem	$itypelist		= $argv[8];
rem	$specialdaysago		= $argv[9];
rem	$filesave		= $argv[10];
rem	$daysago1 		= $argv[11];
rem	$daysago2 		= $argv[12];
rem	$reporttype 		= $argv[13];
echo "running Unique Output Script"

"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\KohaTools\overdueNotices\unique.php" "MAIN" 0 "" "" "" 25 0 "" 0 "c:unique8month.txt" 239 65 "shelf" "PA YV NV"



