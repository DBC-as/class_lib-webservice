<?php
/**
 *
 * This file is part of openLibrary.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark. CVR: 15149043
 *
 * openLibrary is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * openLibrary is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with openLibrary.  If not, see <http://www.gnu.org/licenses/>.
*/

class marc implements Iterator {
  private $marc_array = array();
  private $position = 0;

  public function __construct() {
    $this->position = 0;
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

  function insert($field_array) {
    // find where to insert
    foreach($this->marc_array as $key => $value) {
      if ( $value['field'] > $field_array['field'] ) {
        break;
      }
    }
    $this->position = $key;
	
    $this->marc_array[] = array();
    for ($cnt = count($this->marc_array)-1; $cnt >= $this->position; $cnt--) {
      $this->marc_array[$cnt] = $this->marc_array[$cnt-1];
    }
    $this->marc_array[$this->position] = $field_array;
    $this->position++;
  }


  function fromString($isomarc) {
    $this->marc_array = array();

    $fld = explode($this->fieldTerminator, $isomarc);
    $dummy = array_pop($fld);
    $indx = 0;
    $fldno = '000';
    foreach ($fld as $field) {
      $subfield = explode($this->delimiter, $field);
      $marcar1 = array();
      $marcar1['field'] = $fldno;
      if ( $fldno == '000' ) {
        $marcar1['indicator'] = "";
        $marcar1['subfield'][] = substr(array_shift($subfield), 0, 24);
      }
      else {
        $marcar1['indicator'] = array_shift($subfield);
        $marcar1['subfield'] = $subfield;
      }
      $this->marc_array[] = $marcar1;
      $fldno = substr($isomarc, 24+($indx++*12), 3); 
    }
    return($this->marc_array);
  }

  function getArray() {
    return $this->marc_array;
  }

  function fromArray($marcar) {
    $this->marc_array = $marcar;
    return;
  }

  function toString() {
    $headinfo = "name 22";
    foreach ($this->marc_array as $field) {
      if ( $field['field'] == '004' ) {
        foreach($field['subfield'] as $subfield) {
          if ( $subfield[0] == 'r') {
            $headinfo[0] = $subfield[1];
          }
          if ( $subfield[0] == 'a') {
            $headinfo[3] = $subfield[1];
          }
        }
      }
      if ($field['field'] == '008') {
        foreach ($field['subfield'] as $subfield) {
          if ( $subfield[0] == 't') {
            $headinfo[2] = $subfield[1];
          }
        }
      }
      if ( $field['field'] == '009' ) {
        foreach ($field['subfield'] as $subfield) {
          if ($subfield[0] == 'a') {
            $headinfo[1] = $subfield[1];
          }
        }
      }
    }      
    $total = 0;
    $cntfields = 0;
    $adrss = "";
    foreach($this->marc_array as $field) {
      echo "field:" . $field['field'] . "\n";
      if ( $field['field'] == '000' ) {
        $headinfo = substr($field['subfield'][0],5,7);
        continue;
      }
      $cntfields++;
      $data .= $field['indicator'];
      $lngth = strlen($field['indicator']);
      foreach ($field['subfield'] as $subfield) {
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
    $head = substr($total+100000,1,5) . $headinfo . substr($adrslngth+100000,1,5) . "   45  ";
    return $head . $adrss . $data . $this->endOfRecord;
  }

}
?>