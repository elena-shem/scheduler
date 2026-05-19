<?php
class Model_Custom_Question extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'title',
        'question_html',
        'created_at',
        'updated_at',
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

    protected static $_table_name = 'custom_question';

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('title', 'Title', 'required');
        $val->add_field('question_html', 'Question Html', 'required');


        return $val;
    }

}
