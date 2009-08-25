<?php
require_once("tokenizer_class.php");

class cql2solr extends tokenizer {

	var $tokenlist;
	var $dom;
	var $map;

  function cql2solr($xml) {
		$this->dom=new DomDocument();
		$this->dom->Load($xml);

    $this->case_insensitive=TRUE;
    $this->split_expression="/([ ()=])/";
    $this->operators=$this->get_operators();
    $this->indexes=$this->get_indexes();
    $this->ignore=array("/^prox\//");

		$this->map=array(
		"and"=>"AND",
		"not"=>"NOT",
		"or"=>"OR",
		"="=>":"
		);
	}


	function get_indexes() {
		$indexInfo = $this->dom->getElementsByTagName('indexInfo');

		$i=0;
		foreach ($indexInfo as $indexinfo_key) {
  		$index = $indexInfo->item($i)->getElementsByTagName('name');

  		// get set attribs
  		$j=0;
  		foreach ($index as $index_key) {
        $indexes[]=$index->item($j)->getAttribute('set').".".$index->item($j)->nodeValue;
    		$j++;
  		}

  		$i++;
		}
		return $indexes;
	}

	function get_operators() {
		$supports = $this->dom->getElementsByTagName('supports');

    $i=0;
    foreach ($supports as $support_key) {
			$type=$supports->item($i)->getAttribute('type');
			if($type=="booleanModifier" || $type=="relation")
      	$operators[] = $supports->item($i)->nodeValue;
    
			$i++;
		}
		return $operators;
	}

	function dump() {
		echo "<PRE>";
		print_r($this->tokenlist);
	}


	function convert($query) {

		$return="";
    $this->tokenlist=$this->tokenize(str_replace('\"','"',$query));

    foreach($this->tokenlist as $k=>$v) {

      if($v["type"]=="OPERATOR") {
      	$string.= $this->map[strtolower($v["value"])];
      } else {
				$string.=$v["value"];
			}
    }
		return $string;
  }
}

?>
