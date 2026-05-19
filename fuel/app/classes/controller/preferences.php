<?php
class Controller_Preferences extends Controller_Template
{

	public function action_index()
	{
        
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

        $id_pieces = explode("|", Crypt::decode(Input::get('cll')));
        if(empty($id_pieces[0]) || empty($id_pieces[1]) || empty($id_pieces[2])){
            Response::redirect('welcome');
        }
        $doctoral_id = $id_pieces[0];
        $email_id = $id_pieces[1];
        $examperiod_id = $id_pieces[2];
        $hash = Input::get('token');
        $email_url = Model_Emailurl::query()->where('doctoral_id', $doctoral_id)->where('mail_id', $email_id)->get_one();

        //get today
        $today = date('Y-m-d h:i:s', time());

        //check if links are open through global variable
        $globalsettings = Model_Globalsetting::find('last');

        if (
            ((!is_null($email_url)) && ($hash === $email_url->token) && (($email_url->valid_until >= $today && (1 != $globalsettings->overridelinkexpirationdate)) || (1 == $globalsettings->overridelinkexpirationdate && $globalsettings->linkexpirationdate >= $today)))
            ||
            ((!is_null($email_url)) && ($hash === $email_url->token) && (1 == $globalsettings->alllinksopen))
        ) {
            // Url Is Valid
            // use it
            $email_url->used = 1;
            $email_url->save();
            
            $this->do_stuff($doctoral_id, $examperiod_id);
        } else {
            // Incorrect Url
            Response::redirect('welcome');
        }
    }
    
    
    public static function getUrl()
    {
        $url = 'preferences';
        $flag = true;
        foreach(Input::get() as $param => $value)
        {
            if($flag == true)
            {
                $url .= '?';
                $flag = false;
            }
            else
            {
                $url .= '&';
            }
            $url .= $param.'='.$value;
        }
        return $url;
    }
    
    
    private function do_stuff($doctoral_id, $examperiod_id)
    {
        $examperiod = Model_Examperiod::find($examperiod_id);
        if($examperiod == null)
        {
            // The examperiod id given doesn't correspond to an `examperiods` id
            // in the database.
            
            Session::set_flash('error', 'The requested exam period does not exist');
            Response::redirect(static::getUrl());
        }
        
        $doctoral = Model_Doctoral::find($doctoral_id);
        if($doctoral == null)
        {
            // The doctoral id given doesn't correspond to an `doctorals` id
            // in the database.
            
            Session::set_flash('error', 'The requested doctoral does not exist');
            Response::redirect(static::getUrl());
        }
        
        // The following array will hold the "examday.id|examhour.id" pairs in
        // which at least one examcourse is given. Structure:
        // "examday.id|examhour.id" => array(examcourse1.id, examcourse2.id, ...)
        // 
        $exam_days_x_hours = array();
        foreach($examperiod->examcourses as $examcourse)
        {
            $key = "{$examcourse['examday_id']}|{$examcourse['examhour_id']}";
            $exam_days_x_hours[$key] []= $examcourse['id'];
        }
        
        $prefkey = "[$doctoral_id][$examperiod_id]";
        
        if(Input::method() == 'POST')
        {
            // Purify the comment for goodness' sake!
            Package::load('fuel-htmlpurifier');
            $purifier_config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifier_config);
            try
            {
                // Create and run validation on some input POST values
                $val = Model_Preferencesgeneral::validate('edit');
                if(!$val->run())
                {
                    $ex = new Util\Exception(); $ex->set($val->error()); throw $ex;
                }
                
                if(array_key_exists($prefkey, $doctoral->preferences))
                {
                    $doctoral->preferences[$prefkey]['density'] = Input::post('density');
                    $doctoral->preferences[$prefkey]['density_day'] = Input::post('density_day');
                    $doctoral->preferences[$prefkey]['comment'] =  $purifier->purify(Input::post('comment'));
                    
                    foreach($doctoral->availabilities as $id => $availability)
                    {
                        if($availability['examperiod_id'] == $examperiod_id){
                            $availability->delete();
                            unset($doctoral->availabilities[$id]);
                        }
                    }
                }
                else
                {
                    $doctoral->preferences[$prefkey] = new Model_Preferencesgeneral(array(
                        'examperiod_id' => $examperiod_id,
                        'density'       => Input::post('density'),
                        'density_day'       => Input::post('density_day'),
                        'comment'       => $purifier->purify(Input::post('comment')),
                    ));
                }
                
                foreach(json_decode(Input::post('available')) as $day_x_hour)
                {
                    foreach($exam_days_x_hours[$day_x_hour] as $course_id)
                    {
                        $doctoral->availabilities []= new Model_Preferencesavailable(array(
                            'examperiod_id' => $examperiod_id,
                            'examcourse_id' => $course_id,
                        ));
                    }
                }
                
                $doctoral->save();
                Session::set_flash('success', e('Οι προτιμήσεις σας αποθηκεύτηκαν επιτυχώς'));
            }
            catch(Util\Exception $ex)
            {
                Session::set_flash('error', $ex->get());
            }
            catch(Exception $ex)
            {
                Session::set_flash('error', e($ex->getMessage()));
            }
            
            Response::redirect(static::getUrl());
        }
        
        // The following array will contain "examday.id|examhour.id" pairs for
        // which the doctoral is available for supervising. This data will be
        // stored as array keys for faster search.
        $availabilities = array();
        
        if(array_key_exists($prefkey, $doctoral->preferences))
        {
            foreach($doctoral->availabilities as $availability)
            {
                if($availability['examperiod_id'] == $examperiod_id)
                {
                    $examcourse_id = $availability['examcourse_id'];
                    $examday_id = $examperiod->examcourses[$examcourse_id]['examday_id'];
                    $examhour_id = $examperiod->examcourses[$examcourse_id]['examhour_id'];
                    
                    $day_x_hour = "$examday_id|$examhour_id";
                    $availabilities[$day_x_hour] = null;
                }
            }
            
            $preferences['density'] = $doctoral->preferences[$prefkey]['density'];
            $preferences['density_day'] = $doctoral->preferences[$prefkey]['density_day'];
            $preferences['comment'] = $doctoral->preferences[$prefkey]['comment'];
        }
        else
        {
            foreach($examperiod->examcourses as $examcourse)
            {
                $examday_id = $examcourse['examday_id'];
                $examhour_id = $examcourse['examhour_id'];
                
                $day_x_hour = "$examday_id|$examhour_id";
                $availabilities[$day_x_hour] = null;
            }
            
            $preferences['density'] = 'sparse';
            $preferences['density_day'] = '0';
            $preferences['comment'] = '';
        }
        
        $data['examperiod'] = $examperiod;
        $data['exam_days_x_hours'] = $exam_days_x_hours;
        $data['availabilities'] = $availabilities;
        $data['preferences'] = $preferences;
        
        $this->template->title = "Welcome $doctoral->name !";
        $this->template->css = array('preferences.css');
        $this->template->js = array('preferences.js');
        $this->template->content = View::forge('preferences/index', $data);
    }
    
}

