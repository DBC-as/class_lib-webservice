<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'stringwriter_class.php';

/*!
    \brief Implements PHPUnit test of the class StringWriter
 
    \author 
        Sune Thomas Poulsen <stp@dbc.dk>
*/
class StringWriterTest extends PHPUnit_Framework_TestCase {
    //----------------------------------------------------------------------------
    //                 Test interface
    //----------------------------------------------------------------------------
    
    public function testConstructor() {
        $var = new StringWriter();
        self::assertEquals( '', $var->result() );
    }

    public function testClear() {
        $var = new StringWriter();
        $var->clear();
        self::assertEquals( '', $var->result() );
        
        $var->write( 'Test' );
        $var->clear();
        self::assertEquals( '', $var->result() );
    }
    
    public function testWrite() {
        $var = new StringWriter();
        $var->write( 'Test' );
        self::assertEquals( 'Test', $var->result() );
    
        $var->write( ' ' );
        $var->write( '&nbsp;' );
        self::assertEquals( 'Test &nbsp;', $var->result() );
    }
}

?>
