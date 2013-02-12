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
 * Parse a cql-search and return the corresponding rpn stack
 *
 */


define('MAX_DEFENSIVE_LOOP', 5000);
define('C_OP', 0);
define('C_NO_OP', 1);
define('C_END', 2);
define('C_INDEX', 3);
define('C_P_START', 4);
define('C_P_END', 5);
define('END_VALUE', '$END$END$');

class Cql2Rpn {

  /** \brief Produce a rpn-stack using the shunting yard algorithm
   *
   */
  public function parse_tokens($tokenlist) {
// 0: advance, 1: stack  and advance, 2: unstack, 3: drop stack and advance, 9:error
//                             OP  NO_OP END INDEX P_START
    $action[C_OP]      = array( 2,  2,    1,  2,    1);
    $action[C_NO_OP]   = array( 2,  2,    1,  1,    1);
    $action[C_END]     = array( 2,  2,    0,  2,    9);
    $action[C_INDEX]   = array( 1,  1,    1,  2,    1);
    $action[C_P_START] = array( 1,  1,    1,  1,    1);
    $action[C_P_END]   = array( 2,  2,    9,  2,    3);

    $out = $stack = $rpn = array();
    $operand_no = $loops = $token_no = 0;
    $stack[0]->state = C_END;
    $tokenlist[] = array('type' => OPERATOR, 'value' => END_VALUE);
    while ($token_no < count($tokenlist)) {
      if ($loops++ > MAX_DEFENSIVE_LOOP) {
        throw new Exception('CQL-1: CQL parse error');
      }
      $token = new stdClass();
      $token->value = trim($tokenlist[$token_no]['value']);
      $token->type = $tokenlist[$token_no]['type'];
      if ((count($stack) == 1 && $token->value == END_VALUE) || 
          ($token->type == OPERAND && $token->value)) {
        if ($operand_no++) {
          $token->type = OPERATOR;
          $token->value = 'NO_OP';
          $token_no--;
        }
      }
      if ($token->type == OPERAND || $token->type == INDEX) {
        if ($token->value) {
          $rpn[] = $token;
        }
        $token_no++;
      }
      else {
        if ($token->type == OPERATOR) {
          $operand_no = 0;
          $token->state = self::set_token_state_from_value($token->value);
        }
        $top_state = $stack[count($stack) - 1]->state;
        switch ($action[$token->state][$top_state]) {
          case 0: 
            $token_no++;
            break;
          case 1: 
            $stack[count($stack)] = $token;
            $token_no++;
            break;
          case 2: 
            $rpn[] = $stack[count($stack) - 1];
            unset($stack[count($stack) - 1]);
            break;
          case 3: 
            unset($stack[count($stack) - 1]);
            $token_no++;
            break;
          case 9: 
            throw new Exception('CQL-2: Unbalanced ()');
            break;
          default: 
            throw new Exception('CQL-3: Internal error: Unhandled cql-state');
        }
      }
    }

    return $rpn;
  }

  private function set_token_state_from_value($value) {
    switch ($value) {
      case '=': return C_INDEX;
      case '<': return C_INDEX;
      case '>': return C_INDEX;
      case '<=': return C_INDEX;
      case '>=': return C_INDEX;
      case 'adj': return C_INDEX;
      case '(':   return C_P_START;
      case ')':   return C_P_END;
      case END_VALUE: return C_END;
      case 'NO_OP': return C_NO_OP;
      default: return C_OP;
    }
  }

  private function __construct() {}
  private function __destruct() {}
  private function __clone() {}
}

?>
