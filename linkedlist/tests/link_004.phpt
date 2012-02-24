--TEST--
link_004: Delete every link in the list
--FILE--
<?php
 
$dir = dirname(__FILE__);
require 'Structures/LinkedList/Double.php';
require 'LinkTester.php';

$xyy = new Structures_LinkedList_Double($tester1);
$xyy->appendNode($tester2);
$xyy->appendNode($tester3);
$xyy->appendNode($tester4);

// Delete all nodes from the list
while ($link = $xyy->rewind()) {
    print "Deleted " . $link->getNumb() . "\n";
    $xyy->deleteNode($link);
}
$link = $xyy->rewind();
print "Done\n";
?>
--EXPECT--
Deleted 1
Deleted 2
Deleted 3
Deleted 4
Done
