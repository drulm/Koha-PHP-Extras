--TEST--
single_link_001: Test linked list constructed with an initial link
--FILE--
<?php
 
$dir = dirname(__FILE__);
require 'Structures/LinkedList/Single.php';
require 'SingleLinkTester.php';

$xyy = new Structures_LinkedList_Single($tester1);
$xyy->appendNode($tester2);
$xyy->appendNode($tester3);
$xyy->appendNode($tester4);

$link = $xyy->current();
print "Current: " . $link->getNumb() . "\n";

print "While: ";
// test iteration with while()
do {
    print $link->getNumb();
} while ($link = $xyy->next());

print "\nRewind: ";
$link = $xyy->rewind();
print $link->getNumb();
print "\n";

print "Foreach: ";
// test foreach() iteration
foreach ($xyy as $bull) {
  print $bull->getNumb();
}

print "\nEnd: ";
$link = $xyy->end();
print $link->getNumb();
print "\n";

print "While (in reverse): ";
// test iteration with while()
do {
    print $link->getNumb();
} while ($link = $xyy->previous());


?>
--EXPECT--
Current: 1
While: 1234
Rewind: 1
Foreach: 1234
End: 4
While (in reverse): 4321
