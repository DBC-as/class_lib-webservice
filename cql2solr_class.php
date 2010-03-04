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

require_once("tokenizer_class.php");

class cql2solr extends tokenizer {

	var $tokenlist;
	var $dom;
	var $map;

  function cql2solr($xml, $config="") {
		$this->dom = new DomDocument();
		$this->dom->Load($xml);

    $this->case_insensitive = TRUE;
    $this->split_expression = "/([ ()=])/";
    $this->operators = $this->get_operators();
    $this->indexes = $this->get_indexes();
    $this->ignore = array("/^prox\//");

		$this->map = array(
		  "and" => "AND",
		  "not" => "NOT",
		  "or" => "OR",
		  "=" => ":"
		);
    if ($config)
      $this->raw_index = $config->get_value("raw_index", "setup");
	}


	function get_indexes() {
		$indexInfo = $this->dom->getElementsByTagName('indexInfo');

		$i = 0;
		foreach ($indexInfo as $indexinfo_key) {
  		$index = $indexInfo->item($i)->getElementsByTagName('name');

  		// get set attribs
  		$j = 0;
  		foreach ($index as $index_key) {
        $indexes[] = $index->item($j)->getAttribute('set').".".$index->item($j)->nodeValue;
    		$j++;
  		}

  		$i++;
		}
		return $indexes;
	}

	function get_operators() {
		$supports = $this->dom->getElementsByTagName('supports');

    $i = 0;
    foreach ($supports as $support_key) {
			$type = $supports->item($i)->getAttribute('type');
			if($type == "booleanModifier" || $type == "relation")
      	$operators[] = $supports->item($i)->nodeValue;
    
			$i++;
		}
		return $operators;
	}

	function dump() {
		echo "<PRE>";
		print_r($this->tokenlist);
	}


 /** \brief Parse a cql-query and build the solr search string
  * @param query the cql-query
  */
	function convert($query, $rank=NULL) {

    $dismax_boost = $this->dismax($rank); 
//var_dump($dismax_boost);

    $dismax_q = "%28";
    $this->tokenlist = $this->tokenize(str_replace('\"','"',$query));
//var_dump($this->tokenlist);

    $search_pid_index = FALSE;
    $and_or_part = TRUE;
    foreach($this->tokenlist as $k => $v) {
      $space = !trim($v["value"]);
      //$url_val = urlencode($v["value"]);  // solr-url in utf-8
      $url_val = urlencode(utf8_decode($v["value"]));  // solr-url in iso-latin-1
      switch ($v["type"]) {
        case "OPERATOR":
          $op = $this->map[strtolower($v["value"])];
          if (in_array($op, array("NOT", "AND", "OR")))
            $and_or_part = $op <> "NOT";
      	  $solr_q .= $op;
          if ($op == "OR" && $dismax_boost && $dismax_terms) {
      	    $dismax_q .= "+AND+" . sprintf($dismax_boost, $dismax_terms) . "%29+" . $op . "+%28";
            unset($dismax_terms);
          } else
      	    $dismax_q .= $op;
          if ($op != ":") 
            $search_pid_index = FALSE;
          break;
        case "OPERAND":
          if ($search_pid_index)
            $url_val = str_replace("%3A", "_", $url_val);
				  $solr_q .= $url_val;
				  $dismax_q .= $url_val;
          if (!$v["raw_index"]) 
            $dismax_terms .= ($and_or_part || $space ? "" : "-") . $url_val;
          break;
        case "INDEX":
          if (strtolower($v["value"]) == "rec.id")
            $url_val = "fedoraNormPid";
          $search_pid_index = $url_val == "fedoraNormPid";
				  $solr_q .= $url_val;
				  $dismax_q .= $url_val;
          break;
      }
    }
    if ($dismax_boost && $dismax_terms)
      $dismax_q .= "+AND+" . sprintf($dismax_boost, $dismax_terms);
    $dismax_q .= "%29";
//var_dump($dismax_terms);
//var_dump($solr_q);
//var_dump($dismax_q); die();
		return array("solr" => $solr_q, "dismax" => $dismax_q);
  }

 /** \brief Build a dismax-boost string setting the dismax parameters:
  * - qf: QueryField - boost on words
  * - pf: PhraseField - boost on phrases
  * - tie: tiebreaker, less than 1
  * @param query the cql-query
  * @param rank the rank-settings
  */
  function dismax($rank) {
    if (!is_array($rank))
      if ($boost = substr($rank, 12)) 
        return '_query_:%%22{!dismax+' . $boost .  '}%s%%22';
      else
        return "";

    $qf = str_replace("%", "%%", urlencode($this->make_boost($rank["word_boost"])));
    $pf = str_replace("%", "%%", urlencode($this->make_boost($rank["phrase_boost"])));
    if (empty($qf) && empty($pf)) return "";

    return '_query_:%%22{!dismax' .
           ($qf ? "+qf='" . $qf . "'" : '') .
           ($pf ? "+pf='" . $pf . "'" : '') .
           ($rank["tie"] ? "+tie=" . $rank["tie"] : "") .
           '}%s%%22';
  }

 /** \brief build a boost string
  * @param boosts boost registers and values
  */
  private function make_boost($boosts) {
    foreach ($boosts as $idx => $val)
      if ($idx && $val)
        $ret .= ($ret ? " " : "") . $idx . "^" . $val;
    return $ret;
  }
}

?>
