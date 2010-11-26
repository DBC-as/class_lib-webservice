<?php
require_once 'abstractwriter_class.php';

/*!
    \brief Implements a echo writer class, to write output with echo.
 
    \author 
        Sune Thomas Poulsen <stp@dbc.dk>
*/
class EchoWriter extends AbstractWriter {
    //----------------------------------------------------------------------------
    //                 Public interface
    //----------------------------------------------------------------------------
    
    //!\name Constructor
    //@{
    public function __construct() {
    }
    //@}
          
    //----------------------------------------------------------------------------
    //                 Protected interface
    //----------------------------------------------------------------------------
    
    //!\name Writers implementations
    //@{
    protected function doWrite( $str ) {
        echo $str;
    }
    //@}
    
}

?>
