<?php


class Controller_Admin_Examperiods extends Controller_Admin
{
    
    /***************************************************************************
     * 
     */
    
    public function action_index()
    {
        $data['examperiods'] = Model_Examperiod::find('all');
        $this->template->title = "Examperiods";
        $this->template->content = View::forge('admin/examperiods/index', $data);
    }
    
public function action_getschedule($id=null){

	if($id == 52){
	
		$sleep = rand(6000000,10000000);
		usleep($sleep);
		File::download(APPPATH.'/tmp/xml/'.rand(1,5).'.xml','schedule.xml');
	}else{
		Response::redirect('admin/examperiods');
	}
		
}

private function prepare_examperiod_data($mode = 'create')
{
    $raw_hours = Input::post('examhours', []);
    $fixed_hours = [];

    if (is_array($raw_hours)) {
        foreach ($raw_hours as $when) {
            if (!empty($when['range'])) {
                $parts = explode('-', $when['range']);
                if (count($parts) == 2) {
                    $fixed_hours[] = [
                        'range' => $when['range'],
                        'start' => trim($parts[0]),
                        'end'   => trim($parts[1])
                    ];
                }
            }
        }
    }

    $_POST['examhours'] = $fixed_hours;

    $val = Model_Examperiod::validate($mode);
    if (!$val->run($_POST)) {
        $ex = new Util\Exception();
        $ex->set($val->error());
        throw $ex;
    }

    $day_start = Util\Dateformatter::clientToServer(Input::post('start'));
    $day_end   = Util\Dateformatter::clientToServer(Input::post('end'));

    if (strcmp($day_start, $day_end) > 0) {
        throw new Exception('Must apply: `Start Date` <= `End Date`');
    }

    $examhours_prepared = [];
    if (!empty($_POST['examhours'])) {
        $unique = [];

        foreach ($_POST['examhours'] as $when) {
            $start = $when['start'];
            $end   = $when['end'];

            if (strcmp($start, $end) >= 0) {
                throw new Exception('Must apply: `Start Hour` < `End Hour`');
            }

            $key = $start . '|' . $end;

            if (isset($unique[$key])) {
                throw new Exception('Same exam hour pair entered twice');
            }

            $unique[$key] = true;
            $examhours_prepared[$key] = [$start, $end];
        }
    }

    $examdays_prepared = [];
    $day = $day_start;

    while (strcmp($day, $day_end) <= 0) {
        $examdays_prepared[$day] = $day;
        $day = date('Y-m-d', strtotime($day . " + 1 day"));
    }

    return [
        'day_start' => $day_start,
        'day_end'   => $day_end,
        'examhours' => $examhours_prepared,
        'examdays'  => $examdays_prepared,
    ];
}
    
    /***************************************************************************
     * 
     */
    public function action_create()
{
    if (Input::method() == 'POST') {
        try {
            $data = $this->prepare_examperiod_data('create');

            $examperiod = Model_Examperiod::forge([
                'season'        => Input::post('season'),
                'academic_year' => Input::post('academic_year'),
                'start'         => $data['day_start'],
                'end'           => $data['day_end'],
                'comment'       => Input::post('comment'),
                'active'        => 1,
            ]);

            foreach ($data['examhours'] as $pair) {
                $start = $pair[0];
                $end   = $pair[1];

                $examperiod->examhours[] = new Model_Examhour([
                    'start' => $start,
                    'end'   => $end,
                ]);
            }

            foreach ($data['examdays'] as $day) {
                $examperiod->examdays[] = new Model_Examday(['day' => $day]);
            }

            if (!$examperiod->save()) {
                throw new Exception('Could not save examperiod.');
            }

            Session::set_flash('success', e('Added examperiod #' . $examperiod->id));
            Response::redirect('admin/examperiods');

        } catch (Util\Exception $ex) {
            Session::set_flash('error', $ex->get());
            $this->template->set_global('form', Input::post(), false);

        } catch (Exception $ex) {
            Session::set_flash('error', e($ex->getMessage()));
            $this->template->set_global('form', Input::post(), false);
        }
    }

    $this->template->content = View::forge('admin/examperiods/create');
    $this->template->title = "";
    $this->template->css = ['modern-admin.css', 'vendor/jquery-ui/ui-lightness/jquery-ui-1.10.4.min.css', 'vendor/select2/select2.min.css', 'availabilitycalendar.css'];
    $this->template->js = ['vendor/jquery-ui/jquery-ui-1.10.4.min.js', 'vendor/select2/select2.min.js', 'availabilitycalendar.js', 'examperiods.js'];
}
    
    
    /***************************************************************************
     * 
     */
    
    public function action_edit($id = null)
{
    $examperiod = Model_Examperiod::find($id);

    if ($examperiod == null) {
        Session::set_flash('error', 'The requested exam period does not exist (edit)');
        Response::redirect('admin/examperiods');
    }

    if (Input::method() == 'POST') {
        try {
            $data = $this->prepare_examperiod_data('edit');

            $examperiod->season        = Input::post('season');
            $examperiod->academic_year = Input::post('academic_year');
            $examperiod->start         = $data['day_start'];
            $examperiod->end           = $data['day_end'];
            $examperiod->comment       = Input::post('comment');

            if (!$examperiod->save()) {
                throw new Exception('Could not update examperiod #' . $id);
            }

            $current_hours = Model_Examhour::find('all', ['where' => [['examperiod_id', '=', $examperiod->id]]]);
            $keep_hour_keys = [];

            foreach ($current_hours as $hour) {
                $key = substr($hour->start, 0, 5) . '|' . substr($hour->end, 0, 5);
                if (!isset($data['examhours'][$key])) {
                    $hour->delete();
                } else {
                    $keep_hour_keys[$key] = true;
                }
            }

foreach ($data['examhours'] as $key => $pair) {
    if (!isset($keep_hour_keys[$key])) {
        
        list($start_time, $end_time) = explode('|', $key);

        $new_hour = new Model_Examhour([
            'examperiod_id' => $examperiod->id,
            'start'         => $start_time,
            'end'           => $end_time,
        ]);
        $new_hour->save();
    }
}

            $current_days = Model_Examday::find('all', ['where' => [['examperiod_id', '=', $examperiod->id]]]);
            $keep_day_keys = [];

            foreach ($current_days as $day) {
                if (!isset($data['examdays'][$day->day])) {
                    $day->delete();
                } else {
                    $keep_day_keys[$day->day] = true;
                }
            }

            foreach ($data['examdays'] as $day_val) {
                if (!isset($keep_day_keys[$day_val])) {
                    $new_day = new Model_Examday([
                        'examperiod_id' => $examperiod->id,
                        'day'           => $day_val,
                    ]);
                    $new_day->save();
                }
            }

            Model_Examhour::clean();
            Model_Examday::clean();

            Session::set_flash('success', e('Updated examperiod #' . $id));
            Response::redirect('admin/examperiods');

        } catch (Util\Exception $ex) {
            Session::set_flash('error', $ex->get());
            $this->template->set_global('form', Input::post(), false);

        } catch (Exception $ex) {
            Session::set_flash('error', e($ex->getMessage()));
            $this->template->set_global('form', Input::post(), false);
        }
    } else {
        $examperiod->start = Util\Dateformatter::serverToclient($examperiod->start);
        $examperiod->end   = Util\Dateformatter::serverToclient($examperiod->end);

        $examhours = $examperiod->examhours;
        $examperiod->examhours = [];

        foreach ($examhours as $examhour) {
            $examperiod->examhours[] = [
                'start' => substr($examhour['start'], 0, 5),
                'end'   => substr($examhour['end'], 0, 5),
            ];
        }
    }

    $this->template->set_global('examperiod', $examperiod, false);
    $this->template->content = View::forge('admin/examperiods/edit');
    $this->template->title = "";
}
    
    
    /***************************************************************************
     * 
     */
    
    public function action_delete($id = null)
    {
        $examperiod = Model_Examperiod::find($id);
        if($examperiod != null)
        {
            $examperiod->delete();
            Session::set_flash('success', e('Deleted examperiod #'.$id));
        }
        else
        {
            Session::set_flash('error', e('Could not delete examperiod #'.$id));
        }
        
        Response::redirect('admin/examperiods');
    }
    
    
    /***************************************************************************
     * 
     */
    
    public function action_schedule($id = null)
    {
        $examperiod = Model_Examperiod::find($id);
        if($examperiod == null)
        {
            // The examperiod id given doesn't correspond to an `examperiods` id
            // in the database.
            
            Session::set_flash('error', 'The requested exam period does not exist (schedule)');
            Response::redirect('admin/examperiods');
        }
        
        $courses = Model_Course::find('all');
        
        if(Input::method() == 'POST')
        {
            // If invalid input format (not JSON) was given, then `to_array()`
            // returns an empty array
            $schedule = Format::forge(Input::post('schedule', array()), 'json')->to_array();
            
            // If the given input's format was invalid, then do not continue
            // with this POST request. Redirect to the scheduler page to force
            // the loading of the previous/stored schedule
            if(empty($schedule) && Input::post('schedule') != null)
            {
                Session::set_flash('error', e('Wrong format input (not valid JSON)'));
                Response::redirect('admin/examperiods/schedule/'.$id);
            }
            
            
            try
            {
                // Input validation.
                // Checks if the triad (examday,examhour,course) is valid:
                // * The examdays must exist and be part of this examperiod
                // * The examhours must exist and be part this examperiod
                // * The courses must exist
                {
                    foreach($schedule as $examcourse)
                    {
                        if(!array_key_exists($examcourse[0], $examperiod->examdays))
                        {
                            throw new Exception('Invalid examday id');
                        }
                        else if(!array_key_exists($examcourse[1], $examperiod->examhours))
                        {
                            throw new Exception('Invalid examhour id');
                        }
                        else if(!array_key_exists($examcourse[2], $courses))
                        {
                            throw new Exception('Invalid course id');
                        }
                    }
                }
                
                 
                // The slots array organises and contains the stored and requested
                // courses on specific days and hours. For example the following
                // expression:
                //  $slots['1|2']['stored'][3]
                // Means:
                //  '1' is the examday id.
                //  '2' is the examhour id.
                //  'stored' is the array of stored courses for this slot.
                //  '3' is the course id.
                // The final value can be either an Examcourse model or null.
                $slots = array();
                
                // Builds the slots array.
                foreach($examperiod->examdays as $day_id => $examday) {
                    foreach($examperiod->examhours as $hour_id => $examhour) {
                        $key = "$day_id|$hour_id";
                        $slots[$key] = array(
                            'stored'    => array(),
                            'requested' => array(),
                        );
                    }
                }
                
                // Fills the slots array with stored per slot courses.
                foreach($examperiod->examcourses as $_ => $examcourse) {
                    $examday_id     = $examcourse['examday_id'];
                    $examhour_id    = $examcourse['examhour_id'];
                    $course_id      = $examcourse['course_id'];
                    $key            = "$examday_id|$examhour_id";
                    $slots[$key]['stored'][$course_id] = $examcourse;
                }
                
                // Fills the slots array with requested per slot courses.
                foreach($schedule as $examcourse) {
                    $examday_id     = $examcourse[0];
                    $examhour_id    = $examcourse[1];
                    $course_id      = $examcourse[2];
                    $key            = "$examday_id|$examhour_id";
                    $slots[$key]['requested'][$course_id] = null;
                }
                
                foreach($slots as $day_hour => $slot) {
                    list($examday_id, $examhour_id) = explode('|', $day_hour);
                    
                    foreach($slot['requested'] as $course_id => $_) {
                        
                        if(array_key_exists($course_id, $slot['stored'])) {
                            // Course already stored on requested slot.
                            // Nothing to do.
                            
                        } else if($courses[$course_id]->code != 'DUM') {
                            // Normal course is not stored on requested slot.
                            // Add it.
                            
                            $model = new Model_Examcourse(array(
                                'examperiod_id' => $examperiod->id,
                                'examday_id'    => $examday_id,
                                'examhour_id'   => $examhour_id,
                                'course_id'     => $course_id,
                                'assignments'   => '',
                            ));
                            $model->save();
                            
                            $examperiod->examcourses[$model->id] = $model;
                            $slot['stored'][$course_id] = $model;
                            
                            // If there was another stored course on requested slot.
                            if(count($slot['stored']) > 0) {
                                $prev_model = array_values($slot['stored'])[0];
                                //$model->assignments = $prev_model->assignments;
                                
                                // Copy doctoral availabilities from stored examcourse.
                                foreach($prev_model->available_doctorals as $model_preferences_available) {
                                    $m = new Model_Preferencesavailable(array(
                                        'doctoral_id'   => $model_preferences_available->doctoral_id,
                                        'examperiod_id' => $model_preferences_available->examperiod_id,
                                        'examcourse_id' => $model->id,
                                    ));
                                    $m->save();
                                }
                                
                                // If the stored examcourse contained the dummy
                                // course then delete it.
                                if($prev_model->course_id == -1) {
                                    $prev_model->delete();
                                }
                            }
                            
                        } else if(count($slot['stored']) == 0) {
                            // Dummy course. Add only if there are no stored
                            // courses on requested slot.
                            
                            $model = new Model_Examcourse(array(
                                'examperiod_id' => $examperiod->id,
                                'examday_id'    => $examday_id,
                                'examhour_id'   => $examhour_id,
                                'course_id'     => $course_id,
                                'assignments'   => '',
                            ));
                            $model->save();
                            
                            $examperiod->examcourses[$model->id] = $model;
                            $slot['stored'][$course_id] = $model;
                        }
                    }
                    
                    // Check for each stored course and if it was not requested,
                    // remove it from the database.
                    foreach($slot['stored'] as $course_id => $examcourse_model) {
                        if(!array_key_exists($course_id, $slot['requested'])) {
                            unset($examperiod->examcourses[$examcourse_model->id]);
                            //$examcourse_model->delete();
                        }
                    }
                }
                
                
                // Stores the updated examcourses of this examperiod
                if($examperiod->save())
                {
                    Model_Examcourse::clean();
                }
                else
                {
                    throw new Exception('Could not save schedule');
                }
                
                Session::set_flash('success', e('Schedule was saved successfully'));
                Response::redirect('admin/examperiods/schedule/'.$id);
            }
            catch(Exception $ex)
            {
                Session::set_flash('error', e($ex->getMessage()));
                $this->template->set_global('form', Input::post(), false);
            }
            
            // Creates an array with the examcourse data given as input,
            // re-arranged as:
            //  [examday.id, examhour.id] => array(course1.id, course2.id, ...)
            // 
            $examcourses = array();
            foreach($schedule as $examcourse)
            {
                $index = $examcourse[0].'.'.$examcourse[1];
                $examcourses[$index] []= $examcourse[2];
            }
        }
        else
        {
            // Creates an array with the data contained in `$examperiod->examcourses`
            // re-arranged as:
            //  [examday.id, examhour.id] => array(course1.id, course2.id, ...)
            // 
            $examcourses = array();
            foreach($examperiod->examcourses as $examcourse)
            {
                $index = $examcourse['examday_id'].'.'.$examcourse['examhour_id'];
                $examcourses[$index] []= $examcourse['course_id'];
            }
        }
        
        $data['examperiod'] = $examperiod;
        $data['examcourses'] = $examcourses;
        $data['courses'] = $courses;
        
        $this->template->title = "Examperiod Schedule";
        $this->template->css = array('schedule.css');
        $this->template->js = array('vendor/redips/redips-drag-min.js', 'schedule.js');
        $this->template->content = View::forge('admin/examperiods/schedule', $data);
    }
    
}

