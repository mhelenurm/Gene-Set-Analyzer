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

$(document).ready(function() {
  function getSeparatorString() {
    return $("#separatorbox input:checked").val();
  }

  function getSeparatorChar() {
    if(getSeparatorString() == "space")
      return " ";
    if(getSeparatorString() == "return")
      return "\n";
    return ",";
  }

  function changeOperatorSymbol() {
    var symbol = "&cup;";
    var freetext = $("#gso-opselect").val();
    if(freetext == "intersect")
      symbol = "&cap;";
    if(freetext == "difference")
      symbol = "&minus;";
    if(freetext == "symdiff")
      symbol = "&#9651;";
    $("#gso-symbol-text").html(symbol);
  }
  $("#gso-opselect").change(function() {
    changeOperatorSymbol();
  });
  changeOperatorSymbol();

  function parseInputArray(data) {
    return data.replace(/=/g, getSeparatorChar());
  }

  function hideAllThings() {
    $("#container > div").hide();
  }

  function showSelected() {
    $($("#toolul > li.selected > a").attr("href")).show();
  }

  $("#toolul > li").click(function() {
    var sel_class = "selected";
    $("#toolul > li.selected").removeClass(sel_class);
    $(this).addClass(sel_class);
    hideAllThings();
    showSelected();
  });

  hideAllThings();
  showSelected();

  $("#gso-go").click(function(event) { //GENE SET OPERATOR
    event.preventDefault();
    var textA = $("#gso-in1").val();
    var textB = $("#gso-in2").val();
    var opcode = $("#gso-opselect").val();
    var outsym = "#gso-out";
    $(outsym).val("Loading...");
    $.post("new_op.php", {setA:textA,setB:textB,operation:opcode,sep:getSeparatorString()}).done(function(data) {
      var pdat = $.parseJSON(data);
      $(outsym).val(parseInputArray(pdat.data));
      $("#gso-Act").html(pdat.Act);
      $("#gso-Bct").html(pdat.Bct);
      $("#gso-rct").html(pdat.outct);
    }).fail(function() {
      $(outsym).val("Failed to load for unknown reason.");
    });
  });

  $("#gts-go").click(function(event) { //GENE TO SYNONYMS
    event.preventDefault();
    var symbol = $("#gts-in").val();
    var species = $("#gts-species").val();
    var outsym = "#gts-out";
    $(outsym).val("Loading...");
    $.post("new_syn.php", {syn:symbol,sep:getSeparatorString(),specie:species}).done(function(data) {
      $(outsym).val(data);
    }).fail(function() {
      $(outsym).val("Failed to load for unknown reason.");
    });
  });

  $("#glrr-go").click(function(event) { //GENE LIST REDUNDANCY REMOVER
    event.preventDefault();
    var input = $("#glrr-in").val();
    var outsym = "#glrr-out";
    $(outsym).val("Loading...");
    $.post("new_remove.php", {set:input,sep:getSeparatorString()}).done(function(data) {
      var pdat = $.parseJSON(data);

      $(outsym).val(parseInputArray(pdat.data));
      $("#glrr-olct").html(pdat.inct);
      $("#glrr-leftct").html(pdat.outct);
      $("#glrr-rmct").html(pdat.inct-pdat.outct);
    }).fail(function() {
      $(outsym).val("Failed to load for unknown reason.");
    });
  });

  $("#gnihg-go").click(function(event) { //GENES NOT IN HOMOLOGENE
    event.preventDefault();
    var input = $("#gnihg-in").val();
    var outsym = "#gnihg-out";
    $(outsym).val("Loading...");
    $.post("new_notindb.php", {set:input,sep:getSeparatorString()}).done(function(data) {
      $(outsym).val(data);
    }).fail(function() {
      $(outsym).val("Failed to load for unknown reason.");
    });
  });
  $("div.readparagraph").hide();
  $("a.readmore").click(function(event) {
    event.preventDefault();
    $($(this).attr("link")).animate({opacity:'toggle',height:'toggle'}, 'fast');
    if($(this).text() == "(more info)")
      $(this).text("(less info)");
    else
      $(this).text("(more info)");
  });
});
