<?php
/**
 *
 * This file is part of Open Library System.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark. CVR: 15149043
 *
 * Open Library System is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Library System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Open Library System.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
*
*
* $t=new tokenizer();
* $t->split_expression='/[ ]|([()=])/';
* $t->operators=array('^','*','/','+','-');
* $t->indexes=array('function');
* $tokenlist=$t->convert;
*/

class tokenizer {

  /// Token <string>
  var $token;

  /// Expression to split by in preg format <string>
  var $split_expression = '';

  /// List of operators <array>
  var $operators=array();
  /// List of indexes <array>
  var $indexes=array();
  /// List of ignores <array>
  var $ignore=array();
  /// Prefix for operator <array>
  var $index_prefixes=array();

  /// List of tokens <array>
  var $tokenlist=array();

  /// Sets weather operators and indexes are case insensitive <bool>
  var $case_insensitive=FALSE;

  // indexes which shold be searched as phrase
  var $phrase_index=array();

  /** \brief Check if token is operator.
   *
   * @param token (string)
   * @return (bool)
   *
   */

  function is_operator($token) {
    if ($this->case_insensitive) {
      $token=strtolower($token);
    }

    if (in_array($token,$this->operators)) {
      return TRUE;
    }
    return FALSE;
  }

  /** \brief Check if token is index.
   *
   * @param token (string)
   * @return (bool)
   *
   */

  function is_index($token) {

    if ($this->case_insensitive) {
      $token=strtolower($token);
    }

    if (in_array($token,$this->indexes)) {
      return TRUE;
    }

    foreach($this->index_prefixes as $v) {
      if (in_array(str_replace("$v".'.','',$token), $this->indexes)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  function is_phrase_index($idx) {
    return $this->is_in_index($idx, $this->phrase_index);
  }

  function is_in_index($idx, &$indexes) {
    if (is_array($indexes)) {
      foreach ($indexes as $i) {
        if (substr($idx, 0, strlen($i)) == $i) {
          return TRUE;
    } } }

    return FALSE;
  }

  /** \brief Tokenize string
   *
   * @param string (string)
   * @return (array)
   *
   */

  function tokenize($string) {
    $use_phrase = FALSE;

    $tokens=preg_split($this->split_expression,$string, -1, PREG_SPLIT_DELIM_CAPTURE);

    if ($this->case_insensitive) {
      foreach($this->indexes as $k=>$v)	 {
        $this->indexes[$k]=strtolower($v);
      }
      foreach($this->operators as $k=>$v)	 {
        $this->operators[$k]=strtolower($v);
      }
    }

    foreach($tokens as $k=>$v) {
      if (empty($spos) && isset($v[0]) && $v[0] == '"' && substr_count($v, '"') == 1)
        $spos = $k;
      elseif (isset($spos)) {
        $tokens[$spos] .= $v;
        if (strpos(' '.$v, '"')) unset($spos);
        unset($tokens[$k]);
      }
      $last_token_index=$k;
    }

    //Read a token
    foreach($tokens as $k=>$v) {
      $token=array();

      //If the token is a index token
      if ($this->is_index($v)) {
        $token['type']='INDEX';
        if ($this->is_phrase_index($v)) {
          $use_phrase = TRUE;
        }
        $token['value']=$v;

      }
      else if ($this->is_operator($v)) {
        $token['type']='OPERATOR';
        $token['value']=$v;

      }
      else {

        $ignore=FALSE;

        foreach($this->ignore as $ign) {
          if (preg_match($ign, $v)) {
            $ignore=TRUE;
          }
        }

        if (!$ignore) {
          $token['type']='OPERAND';
          if ($use_phrase) {
            $token['value']=str_replace('"', '', $v) . '"';
            $token['phrase_index']=TRUE;
            $use_phrase = FALSE;
          }
          else
            $token['value']=$v;
        }
      }

      if (!empty($token)) $tokenlist[]=$token;

    }
    return $tokenlist;
  }

}
?>
