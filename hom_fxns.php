<?php
  /*
   The MIT License (MIT)
   
   Copyright (c) 2014 Mark Helenurm
   
   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:
   
   The above copyright notice and this permission notice shall be included in
   all copies or substantial portions of the Software.
   
   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
   THE SOFTWARE.
   */
  
  function clean($text) {
    $text = strip_tags($text);
    $text = htmlspecialchars($text, ENT_QUOTES);
    return ($text); //output clean text
  }
  
  function parseSetData($input) {
    return trim(strtolower($input), " \t\n\r\0\x0B,");
  }
  /*
   This file is the background functions that my gene set analysis website uses. I'm connected to an internal database that I host.
   
   The database has two tables:
   - The first table contains triples of the gene symbol, taxonomic id number, and a homologene gene group id number
   
   - The second table contains pairs of taxonomic id numbers and species names
   
   This file does all the set analysis input data by means of the database.
   */
  //EQUALITY AND CONTAINMENT CHECKING
  function areEqual($gene_symbol_1, $hom_group_1, $gene_symbol_2, $hom_group_2) {
    return $gene_symbol_1 == $gene_symbol_2 || ($hom_group_1 == $hom_group_2 && $hom_group_1 != "-1" && $hom_group_1 != "");
  }
  
  function listContainsGene($list, $gene_symbol, $hom_group) {
    foreach($list as $gene => $id) {
      if(areEqual($gene, $id, $gene_symbol, $hom_group)) {
        return True;
      }
    }
    return False;
  }
  
  //ADDING GENE TO LIST
  function addGeneToList($list, $gene_symbol, $hom_group) {
    $list[$gene_symbol] = $hom_group;
  }
  
  function removeGeneFromList($list, $gene_symbol) {
    unset($list[$gene_symbol]);
  }
  
  //DATABASE SHIT
  function getHomID($mysqli_connection, $gene_symbol) {
    if($result = mysqli_query($mysqli_connection, "SELECT * FROM genes WHERE symbol='".$gene_symbol."' LIMIT 1")) {
      $var = mysqli_fetch_array($result);
      if($var) { //if we find the gene, return the hom_id for the first row of the result
        $retval = $var['hom_id'];
      } else{ //when the gene isn't in our database, return -1
        $retval = "-1";
      }
      mysqli_free_result($result);
      return $retval;
    } else {
      error_log("Failed on gene: " . $gene_symbol);
      return "";
    }
  }
  
  //PARSING AND MAKING DATA
  function getIDGeneList($mysqli_connection, $gene_symbol_list) {
    $array = array();
    foreach($gene_symbol_list as $value) {
      $newvalue = trim($value);
      $id = getHomID($mysqli_connection,$newvalue);
      
      if(!listContainsGene($array, $newvalue, $id)) {
        $array[$newvalue] = $id;
      }
    }
    return $array;
  }
  
  function getNotFoundGenes($mysqli_connection, $symbol_list) {
    $array = array();
    foreach($symbol_list as $value) {
      $newvalue = trim($value);
      $id = getHomID($mysqli_connection,$newvalue);
      if($id === "-1") {
        $array[] = $newvalue;
      }
    }
    
    return $array;
  }
  
  function getIDGeneListUnion($glA, $glB) {
    $array = array();
    foreach($glA as $k => $v) {
      $array[$k] = $v;
    }
    
    foreach($glB as $k => $v) {
      if(!listContainsGene($array, $k, $v)) {
        $array[$k] = $v;
      }
    }
    return $array;
  }
  
  function getIDGeneListIntersection($glA, $glB) {
    $array = array();
    foreach($glA as $k1 => $v1) {
      if(listContainsGene($glB, $k1, $v1)) {
        $array[$k1] = $v1;
      }
    }
    return $array;
  }
  
  function getIDGeneListDifference($glA, $glB) {
    $array = array();
    foreach($glA as $k1 => $v1) {
      if(!listContainsGene($glB, $k1, $v1)) {
        $array[$k1] = $v1;
      }
    }
    return $array;
  }
  
  function getIDGeneListSymmetricDifference($glA, $glB) {
    return getIDGeneListDifference(getIDGeneListUnion($glA, $glB), getIDGeneListIntersection($glA, $glB));
  }
?>
