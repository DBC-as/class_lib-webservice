<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
                 __DIR__ . '/../simpletest' . PATH_SEPARATOR .
                 __DIR__ . '/..');
require_once('simpletest/autorun.php');
require_once('solr_query_class.php');

define('cql_file', '/tmp/simple_test_cql.xml');

class TestOfSolrQueryClass extends UnitTestCase {
  private $c2s;

  function __construct() {
    parent::__construct();
    if (@ $fp = fopen(cql_file, 'w')) {
      fwrite($fp, $this->cql_def());
      fclose($fp);
    }
    else {
      throw new Exception('Cannot write tmp-file: ' . cql_file);
    }
    $this->c2s = new SolrQuery(cql_file);
  }

  function __destruct() { 
    unlink(cql_file);
  }

  function test_instantiation() {
    $this->assertTrue(is_object($this->c2s));
  }

  function test_bool() {
    $tests = array('et AND to' => 'et AND to',
                   'et AND to OR tre' => 'et AND to OR tre',
                   'et AND to OR tre AND fire' => 'et AND to OR tre AND fire',
                   'et to OR tre fire' => 'et AND to OR tre AND fire',
                   '(et AND to) OR tre' => 'et AND to OR tre',
                   'et AND (to OR tre)' => 'to OR tre AND et',
                   '(et AND to' => 'CQL-2: Unbalanced ()',
                   'et AND to)' => 'CQL-2: Unbalanced ()');
    foreach ($tests as $send => $recieve) {
      $this->assertEqual($this->get_edismax($send), $recieve);
    }
  }

  function test_field() {
    $tests = array('dkcclphrase.cclphrase=en' => 'dkcclphrase.cclphrase:"en"',
                   'dkcclphrase.cclphrase=en to' => 'dkcclphrase.cclphrase:"en to"',
                   'dkcclphrase.cclphrase=en AND to' => 'dkcclphrase.cclphrase:"en" AND to',
                   'phrase.phrase=en' => 'phrase.phrase:"en"',
                   'phrase.phrase=en to' => 'phrase.phrase:"en to"',
                   'phrase.phrase=en AND to' => 'phrase.phrase:"en" AND to',
                   'dkcclterm.cclterm=en' => 'dkcclterm.cclterm:(en)',
                   'dkcclterm.cclterm=en to' => 'dkcclterm.cclterm:(en AND to)',
                   'dkcclterm.cclterm=en AND to' => 'dkcclterm.cclterm:(en) AND to',
                   'dkcclterm.cclterm=en OR to' => 'dkcclterm.cclterm:(en) OR to',
                   'dkcclterm.cclterm=(en OR to)' => 'dkcclterm.cclterm:(en OR to)',
                   'term.term=en' => 'term.term:(en)',
                   'term.term=en to' => 'term.term:(en AND to)',
                   'term.term=en AND to' => 'term.term:(en) AND to',
                   'term.term=en OR to' => 'term.term:(en) OR to',
                   'term.term=(en OR to)' => 'term.term:(en OR to)',
                   'phrase.xxx=to' => 'CQL-4: Unknown register',
                   'term.xxx=to' => 'CQL-4: Unknown register');
    foreach ($tests as $send => $recieve) {
      $this->assertEqual($this->get_edismax($send), $recieve);
    }
  }

  function get_edismax($cql) {
    $help = $this->c2s->cql_2_edismax($cql);
    if (isset($help['edismax'])) {
      return $help['edismax'];
    }
    if (isset($help['error'])) {
      return $help['error'];
    }
    return 'no reply';
  }

  function cql_def() {
    return
'<explain>
  <indexInfo>
   <index><map><name set="dkcclphrase">cclphrase</name></map></index>
   <index><map><name set="phrase">phrase</name></map></index>
   <index><map><name set="dkcclterm">cclterm</name></map></index>
   <index><map><name set="term">term</name></map></index>
   <index><map><name set="facet">facet</name></map></index>
  </indexInfo>
  <configInfo>
   <supports type="danBooleanModifier">og</supports>
   <supports type="danBooleanModifier">eller</supports>
   <supports type="danBooleanModifier">ikke</supports>
   <supports type="engBooleanModifier">and</supports>
   <supports type="engBooleanModifier">or</supports>
   <supports type="engBooleanModifier">not</supports>
   <supports type="relation">&gt;=</supports>
   <supports type="relation">&gt;</supports>
   <supports type="relation">&lt;=</supports>
   <supports type="relation">&lt;</supports>
   <supports type="relation">=</supports>
   <supports type="relation">adj</supports>
   <supports type="maskingCharacter">?</supports>
   <supports type="maskingCharacter">*</supports>
   <supports type="booleanChar">(</supports>
   <supports type="booleanChar">)</supports>
  </configInfo>
</explain>';
  }
}
?>
