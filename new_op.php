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
  
  $connect = mysqli_connect("localhost","helenurm_geneuse","!4F6.bKV;IP%","helenurm_hombase");
  
  if(mysqli_connect_errno())
  {
    echo "Error connecting to DB!";
  } else {
    include 'hom_fxns.php';
    //get the delimiter
    $sep_chart = array("comma"=>",","space"=>" ","return"=>"\n");
    $delchar = $sep_chart[$_POST["sep"]];
    
    //parse input data
    $setA_data = parseSetData($_POST["setA"]);
    $setB_data = parseSetData($_POST["setB"]);
    
    //explode arrays
    $arrayA = explode($delchar, $setA_data);
    $arrayB = explode($delchar, $setB_data);
    
    //ready output list
    $array = array();
    
    //get ID lists ready
    $parsedArrayA = getIDGeneList($connect, $arrayA);
    $parsedArrayB = getIDGeneList($connect, $arrayB);
    
    //do the actual operation
    $opcode = $_POST["operation"];
    if($opcode == "union") {
      $array = getIDGeneListUnion($parsedArrayA, $parsedArrayB);
    } else if($opcode == "intersect") {
      $array = getIDGeneListIntersection($parsedArrayA, $parsedArrayB);
    } else if($opcode == "difference") {
      $array = getIDGeneListDifference($parsedArrayA, $parsedArrayB);
    } else if($opcode == "symdiff") {
      $array = getIDGeneListSymmetricDifference($parsedArrayA, $parsedArrayB);
    }
    //get returned symbols and sort them
    $returnedarray = array_keys($array);
    sort($returnedarray);
    
    //output data
    echo '{"data":';
    echo '"' . implode("=",$returnedarray) . '",';
    echo '"Act":"' . count($arrayA) . '",';
    echo '"Bct":"' . count($arrayB) . '",';
    echo '"outct":"' . count($array) . '"}';

    mysqli_close($connect);    
  }
  ?>