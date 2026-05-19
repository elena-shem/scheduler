<?php


class Model_Preferencesavailable extends \Orm\Model
{
    
    /***************************************************************************
     * Model's private configuration
     */
    
    protected static $_table_name = 'preferencesavailable';
    
    protected static $_properties = array(
        'id',
        'doctoral_id',
        'examperiod_id',
        'examcourse_id',
        'created_at',
		'updated_at',
    );
    
    protected static $_belongs_to = array(
        'doctoral',
        'examperiod',
        'examcourse',
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
    
    
    /***************************************************************************
     * Model's public functions
     */
    
    /*
    public static function clean()
    {
        foreach(Model_Examday::find('all', array('where' => array(array('examperiod_id', 0)))) as $examday)
        {
            $examday->delete();
        }
    }
    */
    
}

