<?php


class Validation extends \Fuel\Core\Validation
{
    
    /**
     * Is `$val` member of `$array`
     * Use inside a Model:
     * 
     * property_name => array(
     *   'validation' => array(
     *     'inarray' => array('value1', 'value2', $value3)
     *   )
     * )
     * 
     */
    public static function _validation_inarray($val, $array)
    {
        return in_array($val, $array);
    }
    
    
    public static function _validation_inkeys($key, $array)
    {
        return array_key_exists($key, $array);
    }
    
}

