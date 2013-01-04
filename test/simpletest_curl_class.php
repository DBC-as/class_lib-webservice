<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 __DIR__ . '/../simpletest' . PATH_SEPARATOR .
                 __DIR__ . '/..');
require_once('simpletest/autorun.php');
require_once('curl_class.php');

class TestOfCurlClass extends UnitTestCase {
  private $curl;

  function __construct() {
    parent::__construct();
    $this->curl = new curl();
  }

  function __destruct() { }

  function test_instantiation() {
    $this->assertTrue(is_object($this->curl));
  }

  function test_get() {
    $x = $this->curl->get('localhost');
    $this->assertTrue($x);
  }

  function test_nonexisting_get() {
    $x = $this->curl->get('nohostfound');
    $this->assertFalse($x);
    $this->assertTrue($this->curl->get_status('errno') == 6);
  }

  function test_post() {
    $this->curl->set_url('localhost');
    $this->curl->set_post(array('foo' => 'bar'));
    $x = $this->curl->get();
    $this->assertTrue($x);
  }

  function test_multi_get() {
    $this->curl->set_url('localhost/a', 0);
    $this->curl->set_url('localhost/b', 1);
    $x = $this->curl->get();
    foreach ($x as $r)
      $this->assertTrue($r);
  }
}
?>
