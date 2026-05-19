<?php
class Model_Email extends \Orm\Model
{
	protected static $_properties = array(
		'id',
        'examperiod_id',
        'title',
		'html_content',
		'subject',
        'datespan',
		'created_at',
		'updated_at',
	);


    // in a Model_Email which belong to an examperiod
    protected static $_belongs_to = array(
        'examperiod' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Examperiod',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );

    protected static $_has_many = array(
        'emailurls' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Emailurl',
            'key_to' => 'mail_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
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

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
        $val->add_field('examperiod', 'Exam Period Id', 'required');
        $val->add_field('subject', 'Subject', 'required|min_length[3]|max_length[400]');
        $val->add_field('datespan', 'Date span', 'required|numeric_min[0]|numeric_max[100]');
        $val->add_field('html_content', 'Html Content', 'required');
        $val->add_field('title', 'Identification Title', 'required|min_length[3]|max_length[255]');

        return $val;
	}

}
