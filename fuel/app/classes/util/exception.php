<?php

namespace Util;


/**
 * A more generic exception class. Its purpose is to help transfer various
 * object types instead of a simple message through the exception mechanism.
 */
class Exception extends \Exception
{
    private $_object = null;
    
    public function __construct($message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function set($object)
    {
        $this->_object = $object;
    }
    
    public function get()
    {
        return $this->_object;
    }
    
}

