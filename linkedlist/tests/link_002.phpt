--TEST--
link_002: Append links to an initially empty linked list
--FILE--
<?php
 
$dir = dirname(__FILE__);
require 'Structures/LinkedList/Double.php';
require 'LinkTester.php';

$xyy = new Structures_LinkedList_Double();
$xyy->appendNode($tester1);
$xyy->appendNode($tester2);
$xyy->appendNode($tester3);
$xyy->insertNode($tester4, $tester2, true);

$link = $xyy->current();
print $link->getNumb();

// test iteration with while()
while ($link = $xyy->next()) {
    print $link->getNumb();
}
$link = $xyy->rewind();
print "\n";
print $link->getNumb();
print "\n";

// test foreach() iteration
foreach ($xyy as $bull) {
  print $bull->getNumb();
}
?>
--EXPECT--
1423
1
1423
