<?php
class Model_Doctoral extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'surname',
		'email',
        'am',
        'registrationdate',
        'telephone',
        'deleted_at', 
		'active' => array(
            'data_type' => 'boolean',
            'label' => 'Doctoral is Active',
            'form' => array('type' => 'select', 'options' => array('T' => '1', 'F' => '0')),
        ),
        'comment',
		'hours_remaining',
		'hours_completed',
        'graduated',
        'sendemail',
        'suspended',
        'max_assignments',
        'bonus_weight',
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
        'Doctoralob' => array(
            'events' => array('before_update','before_insert'),
        )
	);

    protected static $_many_many = array(
        'professors' => array(
            'key_from' => 'id',
            'key_through_from' => 'doctoral_id', 
            'table_through' => 'doctoralsupervisors', 
            'key_through_to' => 'professor_id',
            'model_to' => 'Model_Professor',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    
    protected static $_has_many = array(
        'supervisions' => array(
            'key_from' => 'id',
            'model_to' => 'Model_ExamSupervision',
            'key_to' => 'doctoral_id',
            'cascade_delete' => false,
        ),
        'availabilities' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Preferencesavailable',
            'key_to'         => 'doctoral_id',
            'cascade_delete' => true,
        ),
        'preferences' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Preferencesgeneral',
            'key_to'         => 'doctoral_id',
            'cascade_delete' => true,
        ),
        'emailurls' => array(
        'key_from'       => 'id',              // поле в таблице doctoral
        'model_to'       => 'Model_Emailurl',  // модель ссылок
        'key_to'         => 'doctoral_id',     // поле в таблице emailurls
        'cascade_delete' => false,             
        ),
    );
    
    protected static $_conditions = array(
        'order_by' => array(
            'surname' => 'asc',
            'name'    => 'asc',
        ),
    );
    
	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('name', 'Name', 'required|max_length[128]');
		$val->add_field('surname', 'Surname', 'required|max_length[128]');
		$val->add_field('email', 'Email', 'required|valid_emails');
        $val->add_field('am', 'AM', 'max_length[254]');
        $val->add_field('registrationdate', 'Registration Date', 'max_length[254]');
        $val->add_field('telephone', 'Telephone', 'max_length[254]');
		$val->add_field('hours_remaining', 'Hours Remaining', 'required|valid_string[numeric]');
		$val->add_field('hours_completed', 'Hours Completed', 'required|valid_string[numeric]');
        $val->add_field('max_assignments', 'Max Assignments Per Schedule', 'required|valid_string[numeric]');
        $val->add_field('bonus_weight', 'Bonus Weight Per Course', 'required|numeric_min[-10000]|numeric_max[10000]');

		return $val;
	}
    
}
