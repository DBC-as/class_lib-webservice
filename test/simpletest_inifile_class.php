<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 __DIR__ . '/../simpletest' . PATH_SEPARATOR .
                 __DIR__ . '/..');
require_once('simpletest/autorun.php');
require_once('inifile_class.php');

class TestOfInifileClass extends UnitTestCase {
  private $config;
  private $test_ini_name = '/tmp/test_inifile.ini';

  function __construct() {
    parent::__construct();
    if ($fp = fopen($this->test_ini_name, 'w')) {
      fwrite($fp, "[setup]\n\nversion = 1.0\n\nstring = 'abc';\n\n[section]\n\narr[] = 1\narr[] = 2\n");
      fclose($fp);
    }
    $this->config = new inifile($this->test_ini_name);
  }

  function __destruct() { 
    unlink($this->test_ini_name);
  }

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
