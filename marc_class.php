<?php

/**
 * \brief Class for handling Marc (iso and ln) records
 *
 * Example usage:
 * 
 */

class marcException extends Exception {
  
  //  public function __toString() {
  // return "marcException -->".$this-
}

class marc implements Iterator {
  //class marc {
  private $marc_array = array();
  private $position = 0;
  
  var $fp;
  var $marcLength;


  public function __construct() {
    $this->position = 0;
    $this->substitute      = chr(26);
    $this->endOfRecord     = chr(29);
    $this->fieldTerminator = chr(30);
    $this->delimiter       = chr(31);

    return; 
  }
  
  function rewind() {
    $this->position = 0;
  }

  function current() {
    return $this->marc_array[$this->position];
  }

  function key() {
    return $this->position;
  }

  function next() {
    ++$this->position;
  }


  function valid() {
    return isset($this->marc_array[$this->position]);
  }

  function findFields($fieldName) {
    $fields = array();
    foreach($this->marc_array as $value) {
      if ( $value['field'] == $fieldName ) $fields[] = $value;
    }
    return $fields;
  }

  function findSubFields($fieldName,$subFields,$maxres = 9999) {
    $subreturn = array();
    foreach($this->marc_array as $value ) {
      if ( $value['field'] != $fieldName ) continue;
      foreach($value['subfield'] as $subcode) {
	for($cnt = 0; $cnt < strlen($subFields) ; $cnt++) {
	  if ( $subFields[$cnt] == $subcode[0] ) $subreturn[] = substr($subcode,1);
	}
      }
    }
    if ( count($subreturn) > $maxres) 
      throw new marcException("to many result in \"findSubFields\"");
    if ( $maxres == 1 ) 
      return($subreturn[0]);
    else
      return($subreturn);
  }
      

  function readNextMarc() {
    // read next 5 chars:
    if ( ! $marcLength = @fread($this->fp,5) ) {
      if ( ! feof($this->fp)) {
	throw new marcException("reading error");
      }
      else {
	throw new marcException("reading beyond end of medium");
      }
    }
    
    if ( $marcLength[0] == $this->substitute || $marcLength[0] == "\n" ) return false;
    if ( $marcLength < 40 ) throw new marcException("wrong marcLength:$marcLength");

    if ( ! $rest  = @fread($this->fp,$marcLength - 5)) 
      throw new marcException("reading error - something is missing?");

    $this->fromIso($marcLength . $rest);
    
    return true;
  }
  
  function openMarcFile($isofile) {
    if( ! $this->fp = @fopen($isofile,"r") ) {
      throw new marcException("Error while opening file:$isofile");
    }
    //$this->readNextMarc();
  }
    
  function insert($field_array) {
    // find where to insert
    foreach($this->marc_array as $key => $value) {
      if ( $value['field'] > $field_array['field'] ) {
	break;
      }
    }
    $this->position = $key;
	
    $this->marc_array[] = array();
    for($cnt = count($this->marc_array)-1; $cnt >= $this->position; $cnt--) {
      echo "cnt = $cnt\n";
      $this->marc_array[$cnt] = $this->marc_array[$cnt-1];
    }
    $this->marc_array[$this->position] = $field_array;
    $this->position++;
  }
    
  function fromString($marcln) {
     
    if ( is_string($marcln) ) $marcln = explode("\n",$marcln);
    $this->marc_array = array(); 
    foreach($marcln as $ln ) {
      if (strlen($ln) < 2 ) continue;
      $this->field = array();
      $this->field['field'] = substr($ln,0,3);
      $this->field['indicator'] = substr($ln,4,2);
      $this->field['subfield'] = explode("*",substr($ln,7));
      $this->marc_array[] = $this->field;
    }
  }

  function fromIso($isomarc) {
    $this->marc_array = array();
      
    $fld = explode($this->fieldTerminator,$isomarc);
    $dummy = array_pop($fld);
    //print_r($fld); 
      
    $indx = 0;
    $fldno = '000';
    foreach($fld as $field) {
      $subfield = explode($this->delimiter,$field);
      $marcar1 = array();
      $marcar1['field'] = $fldno;
      if ( $fldno == '000' ) {
	$marcar1['indicator'] = "";
	$marcar1['subfield'][] = substr(array_shift($subfield),0,24);
      }
      else {
	$marcar1['indicator'] = array_shift($subfield);
	$marcar1['subfield'] = $subfield;
      }
      $this->marc_array[] = $marcar1;
      $fldno = substr($isomarc,24 + ($indx++*12),3); 
    }
    //print_r($this->marc_array);
    return($this->marc_array);
  }

  function getArray() {
    return $this->marc_array;
  }

  function fromArray($marcar) {
    $this->marc_array = $marcar;
    return;
  }

  function toIso() {
    $headinfo = "name 22";
    foreach($this->marc_array as $field) {
      if ( $field['field'] == '004' ) {
	foreach($field['subfield'] as $subfield) {
	  if ( $subfield[0] == 'r') $headinfo[0] = $subfield[1];
	  if ( $subfield[0] == 'a') $headinfo[3] = $subfield[1];
	}
      }
      if ( $field['field'] == '008' ) {
	foreach($field['subfield'] as $subfield) {
	  if ( $subfield[0] == 't') $headinfo[2] = $subfield[1];
	}
      }
      if ( $field['field'] == '009' ) {
	foreach($field['subfield'] as $subfield) {
	  if ( $subfield[0] == 'a') $headinfo[1] = $subfield[1];
	}
      }
    }      
    $total = 0;
    $cntfields = 0;
    $adrss = "";
    foreach($this->marc_array as $field) {
      //echo "field:" . $field['field'] . "\n";
      if ( $field['field'] == '000' ) {
	$headinfo = substr($field['subfield'][0],5,7);
	continue;
      }
      $cntfields++;
      $data .= $field['indicator'];
      $lngth = strlen($field['indicator']);
      foreach($field['subfield'] as $subfield) {
	$lngth += strlen($subfield) + 1;
	$data .= $this->delimiter . $subfield;
      }
      $lngth++;
      $data .= $this->fieldTerminator;
      $adrss .= $field['field'] . substr($lngth+10000,1,4) . substr($total+100000,1,5);
      $total += $lngth;
    }
    $adrss .= $this->fieldTerminator;
    $total += 25 + strlen($adrss);
    $adrslngth = 25 + $cntfields * 12;
    $head = substr($total+100000,1,5) . $headinfo .
      substr($adrslngth+100000,1,5) . "   45  ";
    return $head . $adrss . $data . $this->endOfRecord;
  }
}
?>