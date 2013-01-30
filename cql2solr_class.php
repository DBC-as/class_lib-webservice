<?php
/**
 *
 * This file is part of Open Library System.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark.
 *
 * Open Library System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Open Library System.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 *
 * DEPRECATED - Replaced by solr_query_class
 *
 */

require_once('tokenizer_class.php');

class cql2solr extends tokenizer {

  var $tokenlist;
  var $dom;
  var $map;
  //var $solr_escapes = array('+','-','&&','||','!','(',')','{','}','[',']','^','"','~','*','?',':','\\');
  var $solr_escapes = array('+', '-', ':', '!', '"');
  var $solr_escapes_from = array();
  var $solr_escapes_to = array();

  public function cql2solr($xml, $config='', $language='') {
    $this->dom = new DomDocument();
    $this->dom->Load($xml);

    $this->case_insensitive = TRUE;
    $this->split_expression = '/(<=|>=|[ <>=()[\]])/';
    $this->operators = $this->get_operators($language);
    $this->indexes = $this->get_indexes();
    $this->ignore = array('/^prox\//');

    if ($language == 'cqldan') {
      $this->map = array('og' => 'AND', 'ikke' => 'NOT', 'eller' => 'OR', '=' => ':', 'adj' => ':');
    }
    else {
      $this->map = array('and' => 'AND', 'not' => 'NOT', 'or' => 'OR', '=' => ':', 'adj' => ':');
    }
    $this->map['<='] = ':';
    $this->map['>='] = ':';
    $this->map['='] = ':';
    $this->map['adj'] = ':';
    $this->map['<'] = ':';
    $this->map['>'] = ':';

    $this->interval = array('<' => '[* TO %s]', 
                            '<=' => '[* TO %s]', 
                            '>' => '[%s TO *]', 
                            '>=' => '[%s TO *]');
    $this->adjust_interval = array('<' => -1, '<=' => 0, '>' => 1, '>=' => 0);

    if ($config) {
      $this->phrase_index = $config->get_value('phrase_index', 'setup');
    }

    foreach ($this->solr_escapes as $ch) {
      $this->solr_escapes_from[] = $ch;
      $this->solr_escapes_to[] = '\\' . $ch;
    }
  }


  private function get_indexes() {
    $indexInfo = $this->dom->getElementsByTagName('indexInfo');

    $i = 0;
    foreach ($indexInfo as $indexinfo_key) {
      $index = $indexInfo->item($i)->getElementsByTagName('name');

      // get set attribs
      $j = 0;
      foreach ($index as $index_key) {
        $indexes[] = $index->item($j)->getAttribute('set').'.'.$index->item($j)->nodeValue;
        $j++;
      }

      $i++;
    }
    return $indexes;
  }

  private function get_operators($language) {
    $supports = $this->dom->getElementsByTagName('supports');

    $boolean_lingo = ($language == 'cqldan' ? 'dan' : 'eng');
    $i = 0;
    foreach ($supports as $support_key) {
      $type = $supports->item($i)->getAttribute('type');
      if ($type == $boolean_lingo . 'BooleanModifier' || $type == 'relation')
        $operators[] = $supports->item($i)->nodeValue;

      $i++;
    }
    return $operators;
  }

  public function dump() {
    echo '<PRE>';
    print_r($this->tokenlist);
  }


  /**
   * Maybe Dijkstra's shunting yard algorithm should be used to analyze the search properly
   */
  private function build_tree($tl) {
    /*
      action 1: Operand is pushed
             2: Operand is popped
             3: Remove operand and pop
             4: Finished
             5: Error


     alfred dc.title "donald duck" = phrase.title peter = OR AND

     NOT
       alfred
       OR
         anders
         peter

    */
    $action = array( '='   => array( ),
                     'AND' => array( ),
                     'NOT' => array( ),
                     'OR'  => array( ),
                     '('   => array( ));

    foreach($this->tokenlist as $k => $v) {

    }
  }
  public function edismax_convert($query, $rank=NULL) {
    $this->tokenlist = $this->tokenize(str_replace('\"','"',$query));
    $num_operands = 0;
    $level_operands = 0;
    $level_paren = 0;
    //if (DEBUG_ON) var_dump($this->tokenlist);
    foreach($this->tokenlist as $k => $v) {
      $trim_value = trim($v['value']);
      switch ($v['type']) {
        case 'OPERATOR':
          if ($v['value'] == 'adj') {
            $proximity = TRUE;
          }
          if (array_key_exists($v['value'], $this->interval)) {
            $interval = $this->interval[$v['value']];
            $interval_adjust = $this->adjust_interval[$v['value']];
          }
          $edismax_q .= $this->map[strtolower($v['value'])];
          $level_operands = 0;
          break;
        case 'OPERAND':
          if ($trim_value == '(') {
            $level_paren++;
            $level_operands = 0;
          }
          elseif ($trim_value == ')') {
            $level_paren--;
            $level_operands = 0;
          }
          elseif ($trim_value) {
            if ($proximity) {
              $add_prox = '~10';
              $proximity = FALSE;
            } 
            elseif ($interval) {
              $v_len = strlen($v['value']) - 1;
              $v['value'] = sprintf($interval, substr($v['value'], 0, $v_len) . 
                                               chr(ord(substr($v['value'],$v_len)) + $interval_adjust));
/* Above will make 1000 to 100/ if adjusted with -1
   Below will instead make 1000 to 999 if adjusted with -1
              if (is_numeric($v['value'])) {
                $v['value'] = sprintf($interval, intval($v['value']) + $interval_adjust);
              }
              else {
                $v['value'] = sprintf($interval, substr($v['value'], 0, $v_len) . 
                                                 chr(ord(substr($v['value'],$v_len)) + $interval_adjust));
              }
*/
              $interval = FALSE;
            }
            elseif ($level_paren && $level_operands) {
              $edismax_q .= ' AND ';
            }
            $level_operands++;
            $num_operands++;
          }
          $edismax_q .= str_replace($this->solr_escapes_from, $this->solr_escapes_to, $v['value']) . $add_prox;
          $add_prox = '';
          break;
        case 'INDEX':
          $current_index = $v['value'];
          $edismax_q .= $v['value'];
          $level_operands = 0;
          break;
      }
      //if (DEBUG_ON) echo $level_paren . ' ' . $num_operands . ' trim: /' . $trim_value . '/ edismax_q: ' . $edismax_q . ' <br/>';
    }
    return array('edismax' => $edismax_q, 'operands' => $num_operands);
  }
  /** \brief Parse a cql-query and build the solr search string
   * @param query the cql-query
   */
  public function convert($query, $rank=NULL) {

    $dismax_boost = $this->dismax($rank);
//var_dump($dismax_boost);

    $dismax_q = '%28';
    $this->tokenlist = $this->tokenize(str_replace('\"','"',$query));
//var_dump($query);
//var_dump($this->tokenlist);
//var_dump($this->build_tree($this->tokellist));


//    $search_pid_index = FALSE;
    $and_or_part = TRUE;
    $num_operands = 0;
    $p_level = 0;
    foreach($this->tokenlist as $k => $v) {
// var_dump($v);
      $space = !trim($v['value']);
      $url_val = urlencode(utf8_decode($v['value']));  // solr-token
      $dismax_val = urlencode(preg_replace('/["()]/', '', utf8_decode($v['value'])));  // dismax-token
      switch ($v['type']) {
        case 'OPERATOR':
          $op = $this->map[strtolower($v['value'])];
          $solr_q .= $op;
//          if ($op != ':') $search_pid_index = FALSE;
          if (in_array($op, array('AND', 'OR', 'NOT'))) {
            if ($op == 'NOT') $NOT_level = $p_level;
            $and_or_part = ($op <> 'NOT');
          }
          if (!isset($NOT_level) && $op == 'OR' && $dismax_boost && $dismax_terms) {
            $dismax_q .= '+AND+' . sprintf($dismax_boost, $dismax_terms) . '%29+' . $op . '+%28';
            unset($dismax_terms);
          }
          else
            $dismax_q .= $op;
          break;
        case 'OPERAND':
          if ($v['value'] == '(') {
            $p_level++;
          }
          elseif ($v['value'] == ')') {
            $p_level--;
          }
          elseif (!$space && isset($NOT_level) && $NOT_level == $p_level) {
            unset($NOT_level);
          }
//          if ($search_pid_index)
//            $url_val = str_replace('%3A', '_', $url_val);
          $url_val = str_replace($this->solr_escapes_from, $this->solr_escapes_to, $url_val);
          $solr_q .= $url_val;
          $dismax_q .= $url_val;
          if (!$v['raw_index'] && trim($dismax_val) && !$space)
            $dismax_terms .= ($and_or_part ? '' : '-') . $dismax_val . urlencode(' ');
          if ($url_val) $num_operands++;
          break;
        case 'INDEX':
//          if (strtolower($v['value']) == 'rec.id')
//            $url_val = 'fedoraNormPid';
//          $search_pid_index = $url_val == 'fedoraNormPid';
          $solr_q .= $url_val;
          $dismax_q .= $url_val;
          break;
      }
    }
    if ($dismax_boost && $dismax_terms)
      $dismax_q .= '+AND+' . sprintf($dismax_boost, $dismax_terms);
    $dismax_q .= '%29';
//var_dump($dismax_terms); var_dump($solr_q); var_dump($dismax_q); die();
    return array('solr' => $solr_q, 'dismax' => $dismax_q, 'operands' => $num_operands);
  }

  /** \brief Build a dismax-boost string setting the dismax parameters:
   * - qf: QueryField - boost on words
   * - pf: PhraseField - boost on phrases
   * - tie: tiebreaker, less than 1
   * @param query the cql-query
   * @param rank the rank-settings
   */
  private function dismax($rank) {
    if (!is_array($rank))
      if ($boost = substr($rank, 12))
        return '_query_:%%22{!dismax+' . $boost .  '}%s%%22';
      else
        return '';

    $qf = str_replace('%', '%%', urlencode($this->make_boost($rank['word_boost'])));
    $pf = str_replace('%', '%%', urlencode($this->make_boost($rank['phrase_boost'])));
    if (empty($qf) && empty($pf)) return '';

    return '_query_:%%22{!dismax' .
           ($qf ? "+qf='" . $qf . "'" : '') .
           ($pf ? "+pf='" . $pf . "'" : '') .
           ($rank['tie'] ? '+tie=' . $rank['tie'] : '') .
           '}%s%%22';
  }

  /** \brief build a boost string
   * @param boosts boost registers and values
   */
  public function make_boost($boosts) {
    if (is_array($boosts)) {
      foreach ($boosts as $idx => $val) {
        if ($idx && $val)
          $ret .= ($ret ? ' ' : '') . $idx . '^' . $val;
      }
    }
    return $ret;
  }
}

?>
