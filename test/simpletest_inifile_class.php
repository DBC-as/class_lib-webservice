<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 __DIR__ . '/../simpletest' . PATH_SEPARATOR .
                 __DIR__ . '/..');
require_once('simpletest/autorun.php');
require_once('inifile_class.php');

class TestOfInifileClass extends UnitTestCase {
  private $config;

  function __construct() {
    parent::__construct();
    $this->config = new inifile(str_replace('.php', '.ini', basename(__FILE__)));
  }

  function __destruct() { }

  function test_instantiation() {
    $this->assertTrue(is_object($this->config));
  }

  function test_simple_var() {
    $this->assertTrue($this->config->get_value('version', 'setup') == '1.0');
    $this->assertTrue(is_string($this->config->get_value('string', 'setup')));
    $this->assertFalse($this->config->get_value('novar', 'setup'));
  }

  function test_struct_var() {
    $this->assertTrue($this->config->get(NULL, 'section') == array('arr' => array('1', '2')));
  }

}
?>
