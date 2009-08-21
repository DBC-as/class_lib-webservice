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

   $last_token_index=count($tokens)-1;

   //Read a token
   foreach($tokens as $k=>$v) {
     $this->token=$v;
			$tokens=array();

	  //If the token is a index token
     if($this->is_index($this->token)) {
				$tokens["type"]="INDEX";
				$tokens["value"]=$this->token;
     
			} else if($this->is_operator($this->token)) {
				$tokens["type"]="OPERATOR";
				$tokens["value"]=$this->token;

			} else {

				$ignore=FALSE;

				foreach($this->ignore as $k=>$v) {
					if(preg_match($v, $this->token)) {
						$ignore=TRUE;
					}
				}

				if(!$ignore) {
					$tokens["type"]="OPERAND";
					$tokens["value"]=$this->token;
				} 
			}

		if(!empty($tokens)) $tokenlist[]=$tokens;

		}
	return $tokenlist;
	}

}
?>
