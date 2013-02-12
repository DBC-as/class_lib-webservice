<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 __DIR__ . '/../simpletest' . PATH_SEPARATOR .
                 __DIR__ . '/..');
require_once('simpletest/autorun.php');
require_once('cql2rpn_class.php');

class TestOfCql2RpnClass extends UnitTestCase {

  function __construct() {
    parent::__construct();
  }

  function __destruct() { 
  }

  function test_simple() {
    $tokens[] = $this->set_arr_value_and_type('et', 'OPERAND');
    $tokens[] = $this->set_arr_value_and_type('', 'OPERAND');
    $tokens[] = $this->set_arr_value_and_type('AND', 'OPERATOR');
    $tokens[] = $this->set_arr_value_and_type('', 'OPERAND');
    $tokens[] = $this->set_arr_value_and_type('to', 'OPERAND');
    $rpn = Cql2Rpn::parse_tokens($tokens);
    $stack[] = $this->set_obj_value_and_type('et', 'OPERAND');
    $stack[] = $this->set_obj_value_and_type('to', 'OPERAND');
    $stack[] = $this->set_obj_value_and_type('AND', 'OPERATOR', 0);
    $this->assertEqual($rpn, $stack);
  }

  function set_arr_value_and_type($value, $type) {
    return array('value' => $value, 'type' => $type);
  }

  function set_obj_value_and_type($value, $type, $state = NULL) {
    $obj = new stdClass();
    $obj->value = $value;
    $obj->type = $type;
    if (!is_null($state)) {
      $obj->state = $state;
    }
    return $obj;
  }
}
?>
