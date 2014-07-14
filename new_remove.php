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
  
  session_start();
  
  $connect = mysqli_connect("localhost","helenurm_geneuse","!4F6.bKV;IP%","helenurm_hombase");
  
  if(mysqli_connect_errno())
  {
    echo "Error connecting to DB!";
  } else {
    include 'hom_fxns.php';
    //save the states of things to the session for reloading the page
    
    //get delimiting character
    $sep_chart = array("comma"=>",","space"=>" ","return"=>"\n");
    $delchar = $sep_chart[$_POST["sep"]];
    
    //clean/parse input data
    $set_data = parseSetData($_POST["set"]);
    
    //explode cleaned list
    $arrayA = explode($delchar, $set_data);
    
    //get the ID list
    $parsedArrayA = getIDGeneList($connect, $arrayA);
    
    //get the list of unique keys
    $returnedarray = array_keys($parsedArrayA);
    
    //sort data
    sort($returnedarray);
    
    //output everything
    echo '{"data":';
    echo '"' . implode("=",$returnedarray) . '",';
    echo '"inct":"' . count($arrayA) . '",';
    echo '"outct":"' . count($parsedArrayA) . '"}';
    
    mysqli_close($connect);
    
  }
  ?>