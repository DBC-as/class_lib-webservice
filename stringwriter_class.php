<?php
require_once 'abstractwriter_class.php';

/*!
    \brief Implements a echo writer class, to store output in a variable.
 
    \author 
        Sune Thomas Poulsen <stp@dbc.dk>
*/
class StringWriter extends AbstractWriter {
    //----------------------------------------------------------------------------
    //                 Public interface
    //----------------------------------------------------------------------------
    
    //!\name Constructor
    //@{
    public function __construct() {
        $this->clear();
    }
    //@}
          
    //!\name Result
    //@{
    public function clear() {
        $this->result = '';
    }
    
    public function result() {
        return $this->result;
    }
    //@}
          
    //----------------------------------------------------------------------------
    //                 Protected interface
    //----------------------------------------------------------------------------
    
    //!\name Writers implementations
    //@{
    protected function doWrite( $str ) {
        $this->result .= $str;
    }
    //@}
    
}

?>
