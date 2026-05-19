<?php
use Orm\Model;

class Model_ExamSupervision extends Model
{
    protected static $_properties = array(
        'id',
        'doctoral_id',
        'examcourse_id',
        'hours',
        'attended',
        'comment',
        'custom_exam_day',
        'custom_exam_hour',
        'created_at',
        'updated_at',
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_table_name = 'exam_supervisions';

    // connections
    protected static $_belongs_to = array(
        'doctoral' => array(
            'key_from' => 'doctoral_id',
            'model_to' => 'Model_Doctoral',
            'key_to' => 'id',
        ),
        'examcourse' => array(
            'key_from' => 'examcourse_id',
            'model_to' => 'Model_Examcourse',
            'key_to' => 'id',
        ),
    );

    // auto-calculation of hours
public function calculate_hours()
{
    $start = null;
    $end = null;

    if (!empty($this->custom_exam_hour)) {
        $parts = preg_split('/[-–—]/u', html_entity_decode($this->custom_exam_hour, ENT_QUOTES, 'UTF-8'));
        
        if (count($parts) >= 2) {
            $start = trim($parts[0]);
            $end = trim($parts[1]);
        }
    }

    if (!$start || !$end) {
        if ($this->examcourse && $this->examcourse->examhour) {
            $start = $this->examcourse->examhour->start;
            $end = $this->examcourse->examhour->end;
        }
    }

    if ($start && $end) {
        $time1 = strtotime($start);
        $time2 = strtotime($end);

        if ($time1 && $time2) {
            $diff_seconds = abs($time2 - $time1);
            return round($diff_seconds / 3600, 2);
        }
    }

    return 0;
}
}
