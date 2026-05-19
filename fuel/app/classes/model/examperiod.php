<?php


class Model_Examperiod extends \Orm\Model
{
    
    /***************************************************************************
     * Model's private configuration
     */
    
    protected static $_properties = array(
        'id',
        'season',
        'academic_year',
        'start',
        'end',
        'active',
        'comment', 
        'created_at',
        'updated_at',
    );
    
    protected static $_has_many = array(
        'examhours' => array(
            'cascade_delete' => true,
        ),
        'examdays' => array(
            'cascade_delete' => true,
        ),
        'examcourses' => array(
            'cascade_delete' => true,
        ),
        'doctoral_preferences' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Preferencesgeneral',
            'key_to'         => 'examperiod_id',
            'cascade_delete' => true,
        ),
        'doctoral_availabilities' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Preferencesavailable',
            'key_to'         => 'examperiod_id',
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
        'order_by' => array('start' => 'desc'),
    );
    
    
    /***************************************************************************
     * Model's public functions
     */
    
    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        
        $val->add('season', 'Season')
            ->add_rule('required')
            ->add_rule('inkeys', static::getSeasons());
        $val->set_message('inkeys', 'The field :label has an invalid value');
        
        $val->add('academic_year', 'Academic Year')
            ->add_rule('required')
            ->add_rule('match_pattern', '/^[0-9]{4}-[0-9]{4}$/');
        
        $val->add('start', 'Start Date')
            ->add_rule('required')
            ->add_rule('valid_date', 'd/m/Y');
        
        $val->add('end', 'End Date')
            ->add_rule('required')
            ->add_rule('valid_date', 'd/m/Y');
        
        $examhours = Input::post('examhours');
        if(is_array($examhours))
        {
            foreach($examhours as $num => $when)
            {
                $val->add("examhours.{$num}.start", "Examhour[{$num}][start]")
                    ->add_rule('required')
                    ->add_rule('match_pattern', '/^[0-9]{2}:[0-9]{2}$/');
                $val->add("examhours.{$num}.end", "Examhour[{$num}][end]")
                    ->add_rule('required')
                    ->add_rule('match_pattern', '/^[0-9]{2}:[0-9]{2}$/');
            }
        }
        
        return $val;
    }

    /**
     * @return array
     * Exam period seasons
     *
     */
    public static function getSeasons()
    {

        $seasons = array(
            'winter' => 'Χειμερινή',
            'summer' => 'Εαρινή',
            'september' => 'Σεπτεμβρίου',
        );
        return $seasons;
    }

    /**
     * @param $season
     * @return string
     * Get the Greek season name.
     */
    public static function getGreekSeason($season){
        switch($season){
            case 'winter':
                return 'Χειμερινή';
            case 'summer':
                return 'Εαρινή';
            case 'september':
                return 'Σεπτεμβρίου';
            default:
                return 'ERROR_PERIOD_NAME';
        }
    }
    
}

