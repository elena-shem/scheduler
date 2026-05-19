<?php


class Controller_Admin_Examcourses extends Controller_Admin
{
    
    /***************************************************************************
     * 
     */
    
    public function action_index()
    {
        Response::redirect('admin/examcourses/edit');
    }
    
    
    /***************************************************************************
     * 
     */
    
    public function action_edit($id = null)
    {
        
        
        $courses = Model_Course::find('all');
        $data['courses'] = array();
        foreach($courses as $course) {
            $data['courses'][$course->id] = $course->title;
        }
        
        $days = Model_Examperiod::query()->select('start', 'end')
                                         ->where('id', 1)
                                         ->get();
        $data['day_start'] = $days[1]['start'];
        $data['day_end'] = $days[1]['end'];
        
        $data['hours'] = Model_Examhour::query()->select('start', 'end')
                                                ->where('period_id', 1)
                                                ->order_by('start')
                                                ->get();
        
        if(Input::method() == 'POST') {
            $course_positions = json_decode(Input::post('course_positions', ''));
            if(empty($course_positions)) {
                $course_positions = array();
            }
            
            $prev_course_positions = Model_Examcourse::find('all', array('where' => array(array('period_id', 1))));
            foreach($prev_course_positions as $pcp) {
                $pcp->delete();
            }
            
            foreach($course_positions as $course_position) {
                $examcourse = Model_Examcourse::forge();
                $examcourse->period_id = 1;
                $examcourse->course_id = substr($course_position[0], 0, -2);
                $examcourse->position = $course_position[1].'.'.$course_position[2];
                $examcourse->day = '2014-01-01';
                $examcourse->hour = 0;
                $examcourse->save();
            }
            
            Response::redirect('admin/examcourses');
        } else {
            $course_positions = Model_Examcourse::find('all', array('where' => array(array('period_id', 1))));
            $arr = array();
            foreach($course_positions as $course_position) {
                if(array_key_exists($course_position->position, $arr)) {
                    $arr[$course_position->position] []= $course_position->course_id;
                } else {
                    $arr[$course_position->position] = array($course_position->course_id);
                }
            }
            $data['course_positions'] = $arr;
        }
        
        $this->template->title = "";
        $this->template->content = View::forge('admin/examcourses/index', $data);
    }
    
}

