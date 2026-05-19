<?php
class Model_Professor extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'surname',
		'email',
        'telephone',
        'office',
		'created_at',
		'updated_at',
        'deleted_at',
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

    protected static $_many_many = array(
        'courses' => array(
            'key_from' => 'id',
            'key_through_from' => 'professor_id', 
            'table_through' => 'professorcourses',
            'key_through_to' => 'course_id', 
            'model_to' => 'Model_Course',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'doctorals' => array(
            'key_from' => 'id',
            'key_through_from' => 'professor_id',
            'table_through' => 'doctoralsupervisors',
            'key_through_to' => 'doctoral_id',
            'model_to' => 'Model_Doctoral',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
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
		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');
        $val->add_field('telephone', 'Telephone', 'required|max_length[32]');
        $val->add_field('office', 'Office', 'required|max_length[32]');

		return $val;
	}

    public function soft_delete()
    {
        $this->deleted_at = time();
        return $this->save();
    }

    public function restore()
    {
        $this->deleted_at = null;
        return $this->save();
    }

    public static function find($id = null, array $options = array())
    {
        if (is_string($id) && ($id === 'all' || $id === 'first'))
        {
            if (!isset($options['where'])) $options['where'] = array();

            $has = false;
            foreach ($options['where'] as $w) {
                if (is_array($w) && isset($w[0]) && $w[0] === 'deleted_at') { $has = true; break; }
            }
            if (!$has) $options['where'][] = array('deleted_at', null);

            return parent::find($id, $options);
        }

        if (is_numeric($id))
        {
            if (!isset($options['where'])) $options['where'] = array();
            $options['where'][] = array('deleted_at', null);
        }

        return parent::find($id, $options);
    }

    public static function find_with_deleted($id = null, array $options = array())
    {
        return parent::find($id, $options);
    }

    public static function find_deleted(array $options = array())
    {
        if (!isset($options['where'])) $options['where'] = array();
        $options['where'][] = array('deleted_at', 'is not', null);

        if (!isset($options['order_by'])) {
            $options['order_by'] = array('surname' => 'asc', 'name' => 'asc');
        }

        return parent::find('all', $options);
    }
}
