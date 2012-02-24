<?php

require 'Structures/LinkedList/Double.php';

/* To do anything useful with a linked list, you need to
 * extend the Node class to hold data associated with the
 * node. In this case, we're just going to hold a single
 * integer in the $_my_number property.
 */
class LinkNodeTester extends Structures_LinkedList_DoubleNode {
    protected $_my_number;
    protected $_my_letter;

    function __construct($number, $letter) {
        $this->_my_number = $number;
        $this->_my_letter = $letter;
    }

    function getNumber() {
        return $this->_my_number;
    }

    function getLetter() {
        return $this->_my_letter;
    }

    function setNumb($number) {
        $this->_my_number = $number;
    }

    function __toString() {
        return "{$this->getNumber()}";
    }
}

/* To enable key=>value iteration, we must override the default key()
 * method in Structures_LinkedList_Double to return a meaningful value */
class LinkListTester extends Structures_LinkedList_Double {
    function key() {
        return $this->current()->getLetter();
    }
}

/* Now we'll create some instances of the new class */
$node1 = new LinkNodeTester(1, 'a');
$node2 = new LinkNodeTester(2, 'b');
$node3 = new LinkNodeTester(3, 'c');
$node4 = new LinkNodeTester(4, 'd');
$node5 = new LinkNodeTester(5, 'e');

/* Start by instantiating a list object.
 * You can either pass the first node to the constructor,
 * or leave it null and add nodes later.
 */
$list = new LinkListTester($node1); // 1

/* appendNode() adds a node to the end of the list */
$list->appendNode($node2);                        // 1-2

/* prependNode() adds a node to the start of the list */
$list->prependNode($node3);                       // 3-1-2

/* insertNode($new_node, $reference_node, $before) adds a node
 * before the reference node if the third parameter is true,
 * or after the reference node if the third parameter is false
 */
$list->insertNode($node4, $node1);              // 3-1-4-2
$list->insertNode($node5, $node1, true);        // 3-5-1-4-2

/* current() returns the current pointer node in the list */
$link = $list->current();
print $link->getNumber(); // "1"

/* rewind() resets the pointer to the root node of the list */
$link = $list->rewind();
print $link->getNumber(); // "3"

// iterate through the list with do...while()
do {
    print $link->getNumber();
} while ($link = $list->next()); // "35142"

/* You can also iterate through a list with foreach() */
foreach ($list as $bull) {
  print $bull->getNumber();
} // 3-1-4-2

/* Override the key() method to enable $key=>$value iteration */
foreach ($list as $key=>$value) {
  print "$key => $value\n";
}

/* end() resets the pointer to the last node of the list */
$link = $list->end();
print $link->getNumber(); // "2"

/* You can iterate backwards through a list with previous() */
do {
    print $link->getNumber();
} while ($link = $list->previous()); // "24153"

?>
