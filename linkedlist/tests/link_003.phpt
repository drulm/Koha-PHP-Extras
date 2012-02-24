--TEST--
link_003: Append links in a specific order
--FILE--
<?php
 
$dir = dirname(__FILE__);
require 'Structures/LinkedList/Double.php';
require 'LinkTester.php';

$xyy = new Structures_LinkedList_Double();

// add initial link in the list
$xyy->appendNode($tester1);
print $tester1->getNumb() . "\n";

// add after initial link
$xyy->appendNode($tester2);
print $tester2->getNumb() . "\n";

// add after initial link, bumping #2 up
$xyy->insertNode($tester3, $tester1);
print $tester3->getNumb() . "\n";

// add after link #3, bumping #2 up again
$xyy->insertNode($tester4, $tester3);
print $tester4->getNumb() . "\n";

print "\n";

$link = $xyy->current();
print $link->getNumb();
print "\n";

// test iteration with while()
while ($link = $xyy->next()) {
    print $link->getNumb();
    print "\n";
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
1
2
3
4

1
3
4
2

1
1342
