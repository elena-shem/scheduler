<?php


class Controller_Admin_Assigner extends Controller_Admin
{
    
    public function action_index()
    {
        $data['examperiods'] = Model_Examperiod::find('all');
        
        if(count(Request::active()->route->segments) == 4) {
            $data['activeExamperiod'] = $data['examperiods'][Request::active()->route->segments[3]];
        } else {
            $data['activeExamperiod'] = reset($data['examperiods']);
        }
        
        $this->template->css = array('assigner.css');
        $this->template->js = array(
            'vendor/jquery-ui/jquery-ui-1.10.4.min.js',
            'assigner/arrmap.js',
            'assigner/examcourse.js',
            'assigner/doctoral.js',
            'assigner/highlighter.js',
            'assigner/assigner.js',
            'assigner/setup.js',
        );
        $this->template->title = "Assigner";
        $this->template->content = View::forge('admin/assigner/index', $data);
    }
    
    
    public function post_save()
    {
        $examperiod = Model_Examperiod::find(Input::post('examperiod', null));
        if($examperiod == null) {
            Session::set_flash('error', 'Invalid examperiod id');
            Response::redirect('admin/assigner/index');
        }
        if($examperiod->active == 0) {
            $title = $examperiod->getGreekSeason($examperiod->season).' '.$examperiod->academic_year;
            Session::set_flash('error', "The exam period \"${title}\" is closed. No changes can be made.");
            Response::redirect('admin/examperiods');
        }
        
        try {
            $assignments = json_decode(Input::post('assignments', null));
            if($assignments == null || !is_array($assignments)) {
                throw new Exception('Parameter "assignments" must be given');
            }
            
            foreach($assignments as $assignment) {
                $assignment->examcourse = Model_Examcourse::find($assignment->examcourseID);
                if($assignment->examcourse == null) {
                    throw new Exception('Invalid examcourse id: "'.$assignment->examcourseId.'".');
                }
            }
            
            foreach($assignments as $assignment) {
                $assignment->examcourse->assignments = implode(',', $assignment->doctoralIDs);
                $assignment->examcourse->save();
            }
            
        } catch (Exception $ex) {
            Session::set_flash('error', $ex->getMessage());
            Response::redirect('admin/assigner/index/'.$examperiod->id);
        }
        
        Session::set_flash('success', 'Assignments saved succesfully');
        Response::redirect('admin/assigner/index/'.$examperiod->id);
    }
    
    
    public function action_close($id)
    {
        $examperiod = Model_Examperiod::find($id);
        if($examperiod == null) {
            // The examperiod id given doesn't correspond to an `examperiods` id
            // in the database.
            Session::set_flash('error', 'The requested exam period does not exist (view)');
            Response::redirect('admin/examperiods');
        }
        if($examperiod->active == 0) {
            // The examperiod is already closed.
            Session::set_flash('error', 'The exam period is already closed');
            Response::redirect('admin/examperiods');
        }
        
        $doctorals = Model_Doctoral::find('all');
        
        foreach($examperiod->examcourses as $examcourse) {
            foreach(explode(",", $examcourse->assignments) as $doctoral_id) {
                if(isset($doctorals[$doctoral_id])) {
                    $doctorals[$doctoral_id]->hours_remaining -= 3;
                    $doctorals[$doctoral_id]->hours_completed += 3;
                }
            }
        }
        
        foreach($doctorals as $doctoral) {
            $doctoral->save();
        }
        
        $examperiod->active = 0;
        $examperiod->save();
        
        Response::redirect('admin/assigner/index/'.$id);
    }
    
}

