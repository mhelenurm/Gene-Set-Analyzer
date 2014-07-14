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
  }
  
  $sep_chart = array("comma"=>",","space"=>" ","return"=>"\n");
  include 'hom_fxns.php';

  if($_POST["specie"] == "")
  {
    $query = mysqli_query($connect, "SELECT * FROM genes WHERE symbol='".clean(strtolower($_POST["syn"]))."'");
    if(!$query)
    {
      echo "query error (internal/not your fault)";
    } else {
      $var = mysqli_fetch_array($query)['hom_id'];
      
      $query2 = mysqli_query($connect, "SELECT * FROM genes WHERE hom_id='".$var."'");
      if(!$query2) {
        echo "query error (internal/not your fault)";
      } else {
        $array = array();
        $thingfound = False;
        while($row = mysqli_fetch_array($query2)) {
          $thingfound = True;
          if(strtolower($row['symbol']) != strtolower($_POST["syn"])) {
            $contains = False;
            foreach($array as $thing) {
              if($thing == strtolower($row['symbol'])) {
                $contains = True;
              }
            }
            if(!$contains) {
              $array[] = strtolower($row['symbol']);
            }
          }
        }
        sort($array);
        if(count($array) != 0 || $thingfound) {
          if(count($array) == 0) {
            echo "No synonyms found!";
          } else {
            echo implode($sep_chart[$_POST["sep"]], $array);
          }
        } else {
          echo "Gene not in database.";
        }
        mysqli_free_result($query2);
      }
      mysqli_free_result($query);
    }
  } else {
    $query = mysqli_query($connect, "SELECT * FROM genes WHERE symbol='".clean($_POST["syn"])."'");
    if(!$query)
    {
      echo "query error (internal/not your fault)";
    } else {
      $row = mysqli_fetch_array($query);
      $var = $row['hom_id'];
      $query2 = mysqli_query($connect, "SELECT * FROM genes WHERE hom_id='".$var."' AND tax_id='" . clean($_POST["specie"]) . "'");
      if(!$query2) {
        echo "query error (internal/not your fault)";
      } else {
        if($row = mysqli_fetch_array($query2)) {
          echo strtolower($row['symbol']);
        } else {
          echo "Gene has no species-specific name in the database.";
        }
        mysqli_free_result($query2);
      }
      mysqli_free_result($query);
    }

  }
  
  mysqli_close($connect);
  
  ?>