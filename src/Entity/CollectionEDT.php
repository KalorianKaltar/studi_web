<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class CollectionEDT {
    
    private $collection;
//    private string $test = 'toto';
    
    public function __construct() {
        $this->collection = new ArrayCollection();
//        $this->elements = $edts;
    }
    
//    public function getTest() {
//        return $this->test;
//    }
    
//    public function getElements() {
//        return $this->elements;
//    }
    
    public function getCollection() {
        return $this->collection;
    }
}

?>
