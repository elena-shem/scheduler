<?php


class Model_Examday extends \Orm\Model
{
    
    /***************************************************************************
     * Model's private configuration
     */
    
    protected static $_properties = array(
        'id',
        'examperiod_id',
        'day',
        'created_at',
        'updated_at',
    );
    
    protected static $_belongs_to = array(
        'examperiod',
    );
    
    protected static $_has_many = array(
        'examcourses' => array(
            'cascade_delete' => true,
        ),
    );
    
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => false,
        ),
    );
    
    protected static $_conditions = array(
        'order_by' => array('day' => 'asc'),
    );
    
    
    /***************************************************************************
     * Model's public functions
     */
    
    public static function clean()
    {
        foreach(Model_Examday::find('all', array('where' => array(array('examperiod_id', 0)))) as $examday)
        {
            $examday->delete();
        }
    }
    
}

