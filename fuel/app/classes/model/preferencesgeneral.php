<?php


class Model_Preferencesgeneral extends \Orm\Model
{
    
    /***************************************************************************
     * Model's private configuration
     */
    
    protected static $_table_name = 'preferencesgeneral';
    
    protected static $_primary_key = array(
        'doctoral_id',
        'examperiod_id',
    );
    
    protected static $_properties = array(
        'id',
        'doctoral_id',
        'examperiod_id',
        'density',
        'density_day',
        'comment',
        'created_at',
        'updated_at',
    );
    
    protected static $_belongs_to = array(
        'doctoral',
        'examperiod',
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
    
    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        
        $val->add('density', 'Density')
            ->add_rule('required')
            ->add_rule('inarray', static::getDensityOptions('density'));
        $val->add('density_day', 'Density Daily')
            ->add_rule('required')
            ->add_rule('inarray', static::getDensityOptions('density_day'));
        $val->set_message('inarray', 'The field :label has an invalid value');
        
        return $val;
    }
    
    
    public static function getDensityOptions($column)
    {
        $options = DB::list_columns('preferencesgeneral', $column);
        $ret = array();
        foreach($options[$column]['options'] as $option)
        {
            $ret[$option] = $option;
        }
        return $ret;
    }
    
}

