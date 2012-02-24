<?php
class LinkTester extends Structures_LinkedList_SingleNode {
    protected $_my_number;

    function __construct($num) {
        $this->_my_number = $num;
    }

    function getNumb() {
        return $this->_my_number;
    }

    function setNumb($numb) {
        $this->_my_number = $numb;
    }
}

$tester1 = new LinkTester(1);
$tester2 = new LinkTester(2);
$tester3 = new LinkTester(3);
$tester4 = new LinkTester(4);
$tester5 = new LinkTester(5);

?>
