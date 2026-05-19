<?php
class Model_Course extends \Orm\Model
{
	protected static $_properties = array(
		'id',
        'special_id',
		'code',
		'code2',
		'title',
		'number_of_supervisors_winter',
		'number_of_supervisors_summer',
		'number_of_supervisors_september',
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
        'professors' => array(
            'key_from' => 'id',
            'key_through_from' => 'course_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'professorcourses', // both models plural without prefix in alphabetical order
            'key_through_to' => 'professor_id', // column 2 from the table in between, should match a users.id
            'model_to' => 'Model_Professor',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
    
    protected static $_has_many = array(
        'examcourses' => array(
            'cascade_delete' => false,
        ),
    );
    
    protected static $_conditions = array(
        'order_by' => array('title' => 'asc'),
    );
    
	public static function validate($factory)
	{
		$val = Validation::forge($factory);
        $val->add_field('special_id', 'Official Course Id', 'required|max_length[254]');
		$val->add_field('code', 'Code', 'required|max_length[64]');
		$val->add_field('code2', 'Code2', 'required|max_length[64]');
		$val->add_field('title', 'Title', 'required|max_length[255]');
		$val->add_field('number_of_supervisors_winter', 'Number Of Supervisors Winter', 'required|valid_string[numeric]');
        $val->add_field('number_of_supervisors_summer', 'Number Of Supervisors Summer', 'required|valid_string[numeric]');
        $val->add_field('number_of_supervisors_september', 'Number Of Supervisors September', 'required|valid_string[numeric]');

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
		// Для 'all' и 'first' добавляем where deleted_at is null, если его нет
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

		// Для find($id) — не отдаём удалённые
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
		$options['where'][] = array('deleted_at', '!=', null);

		if (!isset($options['order_by'])) {
			$options['order_by'] = array('title' => 'asc');
		}

		return parent::find('all', $options);
	}

}
