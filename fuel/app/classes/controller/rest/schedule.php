<?php

class Controller_Rest_Schedule extends \Fuel\Core\Controller_Rest
{
    
    /**
     * http://<hostname>/rest/schedule/list.json?exid=53
     * 
     * Returns a JSON object containing arrays of the days, hours and courses
     * for this examperiod. The arrays must be sorted accordingly.
     */
    public function get_list()
    {
        if(Input::get('exid', null) == null)
        {
            $exid = DB::select('id')
                ->from('examperiods')
                ->order_by('start','desc')
                ->limit(1)
                ->execute();
            $examperiod = Model_Examperiod::find($exid[0]["id"]);
        }
        else
        {
            $examperiod = Model_Examperiod::find(Input::get('exid', null));
        }
        
        if(!$examperiod)
        {
            return $this->response("Invalid examperiod");
        }
        
        $examdays = array();
        foreach($examperiod->examdays as $id => $examday)
        {
            $examdays []= array(
                "id"    => $id,
                "day"   => date('l', strtotime($examday['day'])),
                "date"  => Util\Dateformatter::serverToclient($examday['day']),
            );
        }
        
        $examhours = array();
        foreach($examperiod->examhours as $id => $examhour)
        {
            $examhours []= array(
                "id"    => $id,
                "start" => substr($examhour["start"], 0, 5),
                "end"   => substr($examhour["end"], 0, 5),
            );
        }
        
        $examcourses = array();
        foreach($examperiod->examcourses as $id => $examcourse)
        {
            $course = Model_Course::find($examcourse["course_id"]);
            
            $professors = array();
            foreach($course->professors as $_ => $professor) {
                $professors []= $professor['surname'].' '.$professor['name'];
            }
            
            if($examperiod->season == 'winter') {
                $supsNeeded = $course->number_of_supervisors_winter;
            } else if($examperiod->season == 'summer') {
                $supsNeeded = $course->number_of_supervisors_summer;
            } else if($examperiod->season == 'september'){
                $supsNeeded = $course->number_of_supervisors_september;
            }
            
            $examcourses []= array(
                "id"             => $id,
                "dateId"         => $examcourse["examday_id"],
                "hourId"         => $examcourse["examhour_id"],
                "courseId"       => $examcourse["course_id"],
                "courseName"     => $course->title,
                "courseCode"     => $course->code,
                "professors"     => $professors,
                "courseNameMin"  => static::minifyCourseTitle($course->title),
                "supsNeeded"     => $supsNeeded,
                "assignments"    => preg_split("/,/", $examcourse["assignments"], -1, PREG_SPLIT_NO_EMPTY),
            );
        }
        
        return $this->response(array(
            "active"        => $examperiod->active,
            "dates"         => $examdays,
            "hours"         => $examhours,
            "examcourses"   => $examcourses,
        ));
    }
    
    private static function minifyCourseTitle($title)
    {
        $title = html_entity_decode($title, ENT_COMPAT, 'utf-8');
        return mb_strlen($title, 'utf-8') <= 32 ? $title : mb_substr($title, 0, 32, 'utf-8').' ...';
    }
    
}

