<?php
/*!
    \brief This class defines an abstract interface to write, dump and echo
           strings to stdout or a buffer.
 
    \author 
        Sune Thomas Poulsen <stp@dbc.dk>
*/
abstract class AbstractWriter {
    //----------------------------------------------------------------------------
    //                 Public interface
    //----------------------------------------------------------------------------
    
    //!\name Constructor
    //@{
    public function __construct() {
    }
    //@}
    
    //!\name Writers
    //@{
    public function write( $str ) {
        $this->doWrite( $str );
    }
    
    //@}
   
    //----------------------------------------------------------------------------
    //                 Protected interface
    //----------------------------------------------------------------------------
   
    //!\name Writers implementations
    //@{
    abstract protected function doWrite( $str );
    //@}
    
}

?>
