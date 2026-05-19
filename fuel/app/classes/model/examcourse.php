<?php


class Model_Examcourse extends \Orm\Model
{
    
    /***************************************************************************
     * Model's private configuration
     */
    
    protected static $_properties = array(
        'id',
        'examperiod_id',
        'examday_id',
        'examhour_id',
        'course_id',
        'assignments',
        'created_at',
        'updated_at',
    );
    
    protected static $_belongs_to = array(
        'course' => array(
            'key_from' => 'course_id',
            'model_to' => 'Model_Course',
            'key_to' => 'id',
        ),
        'examperiod' => array(
            'key_from' => 'examperiod_id',
            'model_to' => 'Model_Examperiod',
            'key_to' => 'id',
        ),
        'examday' => array(
            'key_from' => 'examday_id',
            'model_to' => 'Model_Examday',
            'key_to' => 'id',
        ),
        'examhour' => array(
            'key_from' => 'examhour_id',
            'model_to' => 'Model_Examhour',
            'key_to' => 'id',
        ),
    );

    
    protected static $_has_many = array(
        'available_doctorals' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Preferencesavailable',
            'key_to'         => 'examcourse_id',
            'cascade_delete' => true,
        ),
    );
    
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );
    
    
    /***************************************************************************
     * Model's public functions
     */
    
    public static function clean()
    {
        foreach(Model_Examcourse::find('all', array('where' => array(array('examperiod_id', 0)))) as $examcourse)
        {
            $examcourse->delete();
        }
    }
    
}

