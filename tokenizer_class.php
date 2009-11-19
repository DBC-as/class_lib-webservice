<?php

/** 
*
*
* $t=new tokenizer();
* $t->split_expression="/[ ]|([()=])/";
* $t->operators=array("^","*","/","+","-");
* $t->indexes=array("function");
* $tokenlist=$t->convert;
*/

class tokenizer {

	/// Token <string>
  var $token;

	/// Expression to split by in preg format <string>
  var $split_expression = "";

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

 /** \brief Check if token is operator.
  *
  * @param token (string)
  * @return (bool)
  *
  */

  function is_operator($token) {
			if($this->case_insensitive) {
				$token=strtolower($token);
			}

      if(in_array($token,$this->operators)) {
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

			if($this->case_insensitive) {
				$token=strtolower($token);
			}

      if(in_array($token,$this->indexes)) {
        return TRUE;
      }

      foreach($this->index_prefixes as $v) {
      	if(in_array(str_replace("$v".".","",$token), $this->indexes)) {
          return TRUE;
        }
      }

    return FALSE;
  }

 /** \brief Tokenize string
  *
  * @param string (string)
  * @return (array)
  *
  */

  function tokenize($string) {

  $tokens=preg_split($this->split_expression,$string, -1, PREG_SPLIT_DELIM_CAPTURE);

	if($this->case_insensitive) {
		foreach($this->indexes as $k=>$v)	 { $this->indexes[$k]=strtolower($v); }
		foreach($this->operators as $k=>$v)	 { $this->operators[$k]=strtolower($v); }
	}

   foreach($tokens as $k=>$v) {
     if ($v[0] == '"') $spos = $k;
     elseif (isset($spos)) {
       $tokens[$spos] .= $v;
       if (strpos('"', $v)) unset($spos);
       unset($tokens[$k]);
     }
     $last_token_index=$k;
   }

   //Read a token
   foreach($tokens as $k=>$v) {
			$token=array();

	  //If the token is a index token
     if($this->is_index($v)) {
				$token["type"]="INDEX";
        if (strtolower(substr($v, 0, 6) == "facet.")) {
				  $token["value"]='_query_:"{!raw f=' . $v . '}';
          $in_facet = TRUE;
        } else 
				  $token["value"]=$v;
     
			} else if($this->is_operator($v)) {
				$token["type"]="OPERATOR";
        if ($in_facet && $v == '=')
				  $token["value"]='';
        else
				  $token["value"]=$v;

			} else {

				$ignore=FALSE;

				foreach($this->ignore as $ign) {
					if(preg_match($ign, $v)) {
						$ignore=TRUE;
					}
				}

				if(!$ignore) {
					$token["type"]="OPERAND";
          if ($in_facet) {
					  $token["value"]=str_replace('"', '', $v) . '"';
            $in_facet = FALSE;
          } else
					  $token["value"]=$v;
				} 
			}

		if(!empty($token)) $tokenlist[]=$token;

		}
	return $tokenlist;
	}

}
?>
