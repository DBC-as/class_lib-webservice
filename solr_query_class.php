<?php
/**
 *
 * This file is part of Open Library System.
 * Copyright © 2013, Dansk Bibliotekscenter a/s,
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

/**
 * Parse a cql-search and return the corresponding solr-search
 *
 */

require_once('tokenizer_class.php');

define('DEVELOP', FALSE);

define('C_OP', 0);
define('C_NO_OP', 1);
define('C_END', 2);
define('C_INDEX', 3);
define('C_P_START', 4);
define('C_P_END', 5);

class SolrQuery extends tokenizer {

  var $dom;
  var $map;
  // full set of escapes as seen in the solr-doc. We use those who so far has been verified
  //var $solr_escapes = array('+','-','&&','||','!','(',')','{','}','[',']','^','"','~','*','?',':','\\');
  var $solr_escapes = array('+', '-', ':', '!', '"');
  var $solr_escapes_from = array();
  var $solr_escapes_to = array();
  var $phrase_index = array();

  public function __construct($xml, $config='', $language='') {
    $this->dom = new DomDocument();
    $this->dom->Load($xml);

    $this->case_insensitive = TRUE;
    $this->split_expression = '/(<=|>=|[ <>=()[\]])/';
    $this->operators = $this->get_operators($language);
    $this->indexes = $this->get_indexes();
    $this->ignore = array('/^prox\//');

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


  /** \brief Parse a cql-query and build the solr edismax search string
   * 
   */
  public function cql_2_edismax($query) {
    try {
      $tokens = $this->tokenize(str_replace('\"','"',$query));
      if (DEVELOP) { echo 'Query: ' . $query . "\n" . print_r($tokens, TRUE) . "\n"; }
      $rpn = $this->cql_2_rpn($tokens);
      $edismax = $this->rpn_2_edismax($rpn);
    } catch (Exception $e) {
      $edismax = array('error' => $e->getMessage());
    }
    if (DEVELOP) print_r($edismax);
    //if (DEVELOP) die();
    return $edismax;
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

  // ------------------------- Private functions below -------------------------------------

  /** \brief Get list of registers and their types
   * 
   */
  private function get_indexes() {
    $indexes = array(); 
    foreach ($this->dom->getElementsByTagName('indexInfo') as $info_item) {
      foreach ($info_item->getElementsByTagName('name') as $name_item) {
        $indexes[] = $name_item->getAttribute('set').'.'.$name_item->nodeValue;
      }
    }
    return $indexes;
  }

  /** \brief Get list of valid operators
   * @param 
   */
  private function get_operators($language) {
    $operators = array(); 
    $boolean_lingo = ($language == 'cqldan' ? 'dan' : 'eng');
    foreach ($this->dom->getElementsByTagName('supports') as $support_item) {
      $type = $support_item->getAttribute('type');
      if (in_array($type, array('relation', 'booleanChar', $boolean_lingo . 'BooleanModifier'))) {
        $operators[] = $support_item->nodeValue;
      }
    }
    return $operators;
  }


  /** \brief Produce a rpn-stack using the shunting yard algorithm
   * 
   */
  private function cql_2_rpn($tokenlist) {
// 0: advance, 1: stack  and advance, 2: unstack, 3: drop stack and advance, 9:error
//                             OP  NO_OP END INDEX P_START
    $action[C_OP]      = array( 2,  2,    1,  2,    1);
    $action[C_NO_OP]   = array( 2,  2,    1,  1,    1);
    $action[C_END]     = array( 2,  2,    0,  2,    9);
    $action[C_INDEX]   = array( 1,  1,    1,  2,    1);
    $action[C_P_START] = array( 1,  1,    1,  1,    1);
    $action[C_P_END]   = array( 2,  2,    9,  2,    3);

    $END_VALUE = '$END$END$';

    $out = $stack = $rpn = array();
    $stack[0]->state = C_END;
    $tokenlist[] = array('type' => 'OPERATOR', 'value' => $END_VALUE);
    $operand_no = 0;
    $loops = 0;
    $k = 0;
    while ($k < count($tokenlist)) {
      unset($o);
      if ($loops++ > 5000) {
        throw new Exception('CQL-1: CQL parse error');
      }
      if (DEVELOP) { echo "------------------\n$k-$operand_no:\ntoken: " . print_r($tokenlist[$k], TRUE); } 
      $o->value = trim($tokenlist[$k]['value']);
      $o->type = $tokenlist[$k]['type'];
      if ((count($stack) == 1 && $o->value == $END_VALUE) || 
          ($o->type == 'OPERAND' && $o->value)) {
        if ($operand_no++) {
          $o->type = 'OPERATOR';
          $o->value = 'NO_OP';
          $k--;
        }
      }
      if ($o->type == 'OPERAND' || $o->type == 'INDEX') {
        if ($o->value) {
          $rpn[] = $o;
        }
        $k++;
        continue;
      }
      if ($o->type == 'OPERATOR') {
        $operand_no = 0;
        switch ($o->value) {
          case '=': $o->state = C_INDEX; break;
          case '<': $o->state = C_INDEX; break;
          case '>': $o->state = C_INDEX; break;
          case '<=': $o->state = C_INDEX; break;
          case '>=': $o->state = C_INDEX; break;
          case 'adj': $o->state = C_INDEX; break;
          case '(':   $o->state = C_P_START; break;
          case ')':   $o->state = C_P_END; break;
          case $END_VALUE: $o->state = C_END; break;
          case 'NO_OP': $o->state = C_NO_OP; break;
          default: $o->state = C_OP; break;
        }
      }

      $top_state = $stack[count($stack) - 1]->state;
      if (DEVELOP) { echo 'state: ' . $o->state . ' top: ' . $top_state . "\n"; }
      switch ($action[$o->state][$top_state]) {
        case 0: 
          $k++;
          break;
        case 1: 
          $stack[count($stack)] = $o;
          $k++;
          break;
        case 2: 
          $rpn[] = $stack[count($stack) - 1];
          unset($stack[count($stack) - 1]);
          break;
        case 3: 
          unset($stack[count($stack) - 1]);
          $k++;
          break;
        case 9: 
          throw new Exception('CQL-2: Unbalanced ()');
          break;
        default: 
          throw new Exception('CQL-3: Internal error: Unhandled cql-state');
      }
      if (DEVELOP) { echo 'rpn: ' . print_r($rpn, TRUE) . 'stack: ' . print_r($stack, TRUE); }
    }

    return $rpn;
  }

  /** \brief Makes an edismax query from the RPN-stack
   */
  private function rpn_2_edismax($rpn) {
    $folded_rpn = $this->fold_operands($rpn);
    $num_operands = 0;
    foreach ($folded_rpn as $r) {
      if ($r->type == 'OPERAND') {
        $num_operands++;
      }
    }
    $edismax_q = $this->folded_2_edismax($folded_rpn);

    return array('edismax' => $edismax_q, 'operands' => $num_operands);
  }

  /** \brief folds OPERANDs bound to indexes depending on INDEX-type
   */
  private function fold_operands($rpn) {
    $intervals = array('<' => '[* TO %s]', 
                      '<=' => '[* TO %s]', 
                      '>' => '[%s TO *]', 
                      '>=' => '[%s TO *]');
    $adjust_intervals = array('<' => -1, '<=' => 0, '>' => 1, '>=' => 0);
    $curr_index = '';
    $edismax = '';
    $index_stack = array();
    $folded = array();
    $operand->type = 'OPERAND';
    if (DEVELOP) { echo 'fold_op: ' . print_r($rpn, TRUE) . "\n"; }
    foreach ($rpn as $r) {
      if (DEVELOP) { echo $r->type . ' ' . $r->value . ' ' . print_r($operand, TRUE) . "\n"; }
      switch ($r->type) {
        case 'INDEX':
          $curr_index = $r->value;
          break;
        case 'OPERAND':
          $r->value = str_replace($this->solr_escapes_from, $this->solr_escapes_to, $r->value);
          if ($curr_index) {
            $index_stack[] = $r;
          }
          else {
            $folded[] = $r;
          }
          break;
        case 'OPERATOR':
          switch ($r->value) {
            case '<';
            case '>';
            case '<=';
            case '>=';
              if (empty($curr_index)) {
                throw new Exception('CQL-4: Unknown register');
              }
              $interval = $intervals[$r->value];
              $interval_adjust = $adjust_intervals[$r->value];
              $imploded = $this->implode_stack($index_stack);
              $o_len = strlen($imploded) - 1;
              $operand->value = $curr_index . ':' . 
                                sprintf($interval, substr($imploded, 0, $o_len) . 
                                                   chr(ord(substr($imploded,$o_len)) + $interval_adjust));
              if ($operand->value) {
                $folded[] = $operand;
              }
              $curr_index = '';
              $index_stack = array();
              break;
            case '=';
              if (empty($curr_index)) {
                throw new Exception('CQL-4: Unknown register');
              }
              $operand->value = $this->implode_indexed_stack($index_stack, $curr_index);
              if (DEVELOP) { echo 'Imploded: ' . $operand->value . "\n"; }
              if ($operand->value) {
                $folded[] = $operand;
              }
              $curr_index = '';
              $index_stack = array();
              break;
            case 'adj';
              if (empty($curr_index)) {
                throw new Exception('CQL-4: Unknown register');
              }
              $imploded = $this->implode_stack($index_stack);
              $operand->value = $curr_index . ':"' . $imploded . '"~10';
              if ($operand->value) {
                $folded[] = $operand;
              }
              $curr_index = '';
              $index_stack = array();
              break;
            default:
              if ($curr_index) {
                $index_stack[] = $r;
              }
              else {
                if (isset($operand->value) && $operand->value) {
                  $folded[] = $operand;
                }
                $folded[] = $r;
              }
          }
          unset($operand);
          $operand->type = 'OPERAND';
          break;
        default:
          throw new Exception('CQL-5: Internal error: Unknown rpn-element-type');
      }
      if (DEVELOP && ($r->type == 'OPERATOR')) { echo 'folded: ' . print_r($folded, TRUE) . "\n"; }
    }
    if (isset($operand->value) && $operand->value) {
      $folded[] = $operand;
    }
    if (DEVELOP) { echo 'rpn: ' . print_r($rpn, TRUE) . 'folded: ' . print_r($folded, TRUE); }
    return $folded;
  }

  /** \brief Unstacks and set solr-syntax depending on index-type
   */
  private function implode_indexed_stack($stack, $index, $adjacency = '') {
    list($idx_type) = explode('.', $index);
    if (in_array($idx_type, $this->phrase_index)) {
      return $index . ':"' . $this->implode_stack($stack) . '"' . $adjacency;
    }
    else {
      return $index . ':(' . $this->implode_stack($stack, 'AND') . ')' . $adjacency;
    }
  }

  /** \brief Unstacks and set/remove operator between operands
   */
  private function implode_stack($stack, $default_op = '') {
    $ret = '';
    $st = array();
    if ($default_op) {
      $default_op = ' ' . trim($default_op) . ' ';
    }
    else {
      $default_op = ' ';
    }
    foreach ($stack as $s) {
      if ($s->type == 'OPERATOR') {
        if ($s->value <> 'NO_OP') {
          $ret .= $st[count($st)-2] . ' ' . $s->value . ' ' . $st[count($st)-1];
          unset($st[count($st)-1]);
          unset($st[count($st)-1]);
        }
      }
      else {
        $st[count($st)] = $s->value;
      }
    }
    foreach ($st as $s) {
      $ret .= (!empty($ret) ? $default_op : '') . $s;
    }
    return $ret;
  }

  /** \brief Unstack folded stack and produce solr-search
   */
  private function folded_2_edismax($folded) {
    $edismax = '';
    $stack = array();
    foreach ($folded as $f) {
      if (DEVELOP) { echo $f->type . ' ' . $f->value . "\n"; }
      if ($f->type == 'OPERAND') {
        $stack[count($stack)] = $f->value;
      }
      if ($f->type == 'OPERATOR') {
        if ($f->value == 'NO_OP') {
          $f->value = 'AND';
        }
        if (empty($edismax)) {
          $edismax .= $stack[count($stack)-2] . ' ' . $f->value . ' ' . $stack[count($stack)-1] . ' ';
          unset($stack[count($stack)-1]);
        }
        else {
          $edismax .= $f->value . ' ' . $stack[count($stack)-1] . ' ';
        }
        unset($stack[count($stack)-1]);
      }
    }
    if (DEVELOP) { echo 'stack: ' . print_r($stack, TRUE) . "\n"; }
    foreach ($stack as $s) {
      $edismax .= $s . ' ';
    }
    if (DEVELOP) { echo 'ed: ' . $edismax . "\n"; }
    return trim($edismax);
  }
}

?>
