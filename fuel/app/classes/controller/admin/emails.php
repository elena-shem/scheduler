<?php

class Controller_Admin_Emails extends Controller_Admin
{

    public function action_index()
    {
        if (!Auth::has_access('emails.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        $data['emails'] = Model_Email::find('all',array('order_by' => array('id' => 'desc')));
        $this->template->title = "Listing Emails";
        $this->template->content = View::forge('admin/emails/index', $data);

    }

    public function action_view($id = null)
    {
        if (!Auth::has_access('emails.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        if (Input::method() == 'POST')
        {
            $checkbox_urls = \Fuel\Core\Input::post('checkbox_url');
            if(!empty($checkbox_urls)){
                if (isset($_POST['rearm_selected'])) {
                    $this->rearm_selected($id,$checkbox_urls);
                } else if (isset($_POST['delete_selected'])) {
                    $this->delete_selected($id,$checkbox_urls);
                } else{
                }
            }else{
                Session::set_flash('error', e('You did not select anything!'));
            }
        }

        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

        $email = $data['email'] = Model_Email::find($id);
        $exam_period = Model_Examperiod::find($email->examperiod_id);
        if($exam_period == null){
            $data['exam_period_info'] = "None selected!";
        }else{
            $data['exam_period_info'] = $exam_period->getGreekSeason($exam_period->season) . " " . $exam_period->academic_year;
        }

        $once = false;
        $data['doctorals'] = array();
        $data['emails_number'] = 0;
        $data['emails_used'] = 0;
        $data['emails_saved'] = 0;

        foreach ($email->emailurls as $url) {
            $data['emails_number']+=1;
            if($url->used){
                $data['emails_used']+=1;
            }
            if(!$once){
                $once = true;
                $email->valid_date = $url->valid_until;
            }

            $obj = Model_Doctoral::find($url->doctoral_id);
            if(is_null($obj)){
                continue;
            }
            $obj->sent = $url->sent;
            $obj->used = $url->used;
            $obj->url_id = $url->id;
            $tmp_valid_until = strtotime($url->valid_until);
            $tmp_now = time();
            $obj->is_still_valid = true;
            if($tmp_now > $tmp_valid_until){
                $obj->is_still_valid = false;
            }
            $obj->valid_until = date( 'd/m/Y H:i',$tmp_valid_until);

            $obj->updated_at = date( 'd/m/Y H:i',$url->updated_at);

            $obj->has_saved_settings = Model_Preferencesgeneral::query()->where('doctoral_id','=',$url->doctoral_id)->where('examperiod_id','=',$email->examperiod_id)->count();
            if($obj->has_saved_settings){
                $data['emails_saved']+=1;
            }
            //create unique url
            $obj->unique_uri = Uri::create('preferences', array('ids' => Crypt::encode($obj->id . "|" . $email->id . "|" . $email->examperiod_id), 'token' => $url->token), array('cll' => ':ids', 'token' => ':token'));

            array_push($data['doctorals'], $obj);
        }
        //Sort by surname
        usort($data['doctorals'],array($this,"compare_surnames"));

        $this->template->js = array(
            'emails/email-view.js',
            'vendor/sortable.js',
        );
        $this->template->css = array(
            'email-view.css',
        );
        $this->template->title = "";
        $this->template->content = View::forge('admin/emails/view', $data, false);

    }


    private function compare_surnames($a, $b)
    {
        return strcmp($a->surname, $b->surname);
    }


    public function action_create()
    {
        if (!Auth::has_access('emails.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        /*
         * Purify Html (strip javascript actually).
         *
         */
        Package::load('fuel-htmlpurifier');
        $purifier_config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifier_config);

        //load the PasswordLib
        $lib = new PasswordLib\PasswordLib;

        $data['exam_periods'] = array();
        $data['already_selected_exam_period'] = 'none';
        $exam_periods = Model_Examperiod::find('all');
        foreach($exam_periods as $period){
            $data['exam_periods'][$period->id] = $period->getGreekSeason($period->season) . " " . $period->academic_year;
        }

        $data['already_selected_doctoral_ids'] = array();
        $all_active_doctorals = Model_Doctoral::find('all',
            array(
                'where' => array(
                    array('active', 1),
                )
            ));
        $data['all_active_doctorals'] = array();
        foreach ($all_active_doctorals as $doctoral) {
            $data['all_active_doctorals'][$doctoral->id] = $doctoral->name . " " . $doctoral->surname;
        }


        if (Input::method() == 'POST' && Security::check_token()) {
            $html_content = $purifier->purify(Input::post('html_content'));
            $val = Model_Email::validate('create');


            if ($val->run()) {
                $email = Model_Email::forge(array(
                    'examperiod_id' => Input::post('examperiod'),
                    'html_content' => $html_content,
                    'subject' => Input::post('subject'),
                    'title' => Input::post('title'),
                    'datespan' => Input::post('datespan'),
                ));



                if ($email){
                    try{
                        $email->save();

                        //set timezone
                        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

                        //get today
                        $today = date('Y-m-d h:i:s', time());
                        //set max valid date
                        $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $email->datespan days"));

                        $new_doctorals_ids_array = Input::post('doctorals');
                        if (is_array($new_doctorals_ids_array)) {
                            foreach ($new_doctorals_ids_array as $doctoral_id) {
                                if (null == Model_Doctoral::find($doctoral_id)) {
                                    continue;
                                }
                                $new_email_url = new Model_Emailurl();
                                $new_email_url->doctoral_id = $doctoral_id;
                                $new_email_url->token = $lib->getRandomToken(128);
                                $new_email_url->sent = 0;
                                $new_email_url->used = 0;
                                $new_email_url->mail_id = $email->id;
                                $new_email_url->valid_until = $valid_until;
                                try{
                                    $new_email_url->save();
                                }catch (\Database_Exception $e){}
                            }
                        }

                        Session::set_flash('success', e('Added email #' . $email->id . '.'));

                        Response::redirect('admin/emails/view/'.$email->id);


                    }catch (\Database_Exception $e){
                        Session::set_flash('error', e('Could not save email! '.$e->getMessage()));
                    }

                } else {
                    Session::set_flash('error', e('Could not save email.'));
                }


            } else {
                Session::set_flash('error', $val->error());
            }
        }

        $this->template->js = array(
            '/vendor/ckeditor/ckeditor.js',
            '/vendor/ckeditor/adapters/jquery.js',
            'emails/email-editor.js',
        );

        $this->template->title = "";
        $this->template->content = View::forge('admin/emails/create', $data);

    }

    public function action_edit($id = null)
    {
        if (!Auth::has_access('emails.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        Package::load('fuel-htmlpurifier');
        $purifier_config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifier_config);
        //load the PasswordLib
        $lib = new PasswordLib\PasswordLib;


        $email = Model_Email::find($id);

        $email_urls = $email->emailurls;

        $data['already_selected_exam_period'] = $email->examperiod_id;
        $data['exam_periods'] = array();
        $exam_periods = Model_Examperiod::find('all');
        foreach($exam_periods as $period){
            $data['exam_periods'][$period->id] = $period->getGreekSeason($period->season) . " " . $period->academic_year;
        }

        $data['already_selected_doctoral_ids'] = array();
        foreach ($email_urls as $url) {
            $temp_obj_doc = Model_Doctoral::find($url->doctoral_id);
            if(is_null($temp_obj_doc)){
                continue;
            }
            array_push($data['already_selected_doctoral_ids'], $temp_obj_doc->id);
        }

        $all_active_doctorals = Model_Doctoral::find('all',
            array(
                'where' => array(
                    array('active', 1),
                )
            ));
        $data['all_active_doctorals'] = array();
        foreach ($all_active_doctorals as $doctoral) {
            $data['all_active_doctorals'][$doctoral->id] = $doctoral->name . " " . $doctoral->surname;
        }

        $html_content = $purifier->purify(Input::post('html_content'));
        $val = Model_Email::validate('edit');

        if ($val->run()) {
            if (Input::method() == 'POST' && Security::check_token()) {
                $email->html_content = $html_content;
                $email->subject = Input::post('subject');
                $email->title = Input::post('title');
                $email->examperiod_id =  Input::post('examperiod');
                $email->datespan =  Input::post('datespan');

                $new_doctorals_ids_array = Input::post('doctorals');


                if (is_array($new_doctorals_ids_array)) {
                    $already_exist = array();
                    foreach ($email_urls as $url) {
                        $doctoral_id = Model_Doctoral::find($url->doctoral_id)->id;

                        if (in_array($doctoral_id, $new_doctorals_ids_array)) {
                            array_push($already_exist, $doctoral_id); //keep the existing in both
                            continue;
                        } else {
                            //DB::query('DELETE FROM emailurls WHERE id=:id',DB::DELETE)->bind(':id',$url->id)->execute();
                            try{
                                Model_Emailurl::query()->where('id', $url->id)->delete();
                            }catch(\Database_Exception $e){}
                        }
                    }
                    //now add all the new_doctorals that didn't have a match in the db
                    //set timezone
                    date_default_timezone_set(\Fuel\Core\Config::get('timezone'));
                    //get validity time span

                    //get today
                    $today = date('Y-m-d h:i:s', time());
                    //set max valid date
                    $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $email->datespan days"));
                    //change it for all the emails
                    foreach ($email_urls as $url){
                        $url->valid_until = $valid_until;
                        try{
                            $url->save();
                        }catch (\Database_Exception $e){}
                    }
                    //change the diffs
                    $diff = array_diff($new_doctorals_ids_array, $already_exist);
                    foreach ($diff as $doc_id) {
                        $new_email_url = new Model_Emailurl();
                        $new_email_url->doctoral_id = $doc_id;
                        $new_email_url->token = $lib->getRandomToken(128);
                        $new_email_url->sent = 0;
                        $new_email_url->used = 0;
                        $new_email_url->mail_id = $email->id;
                        $new_email_url->valid_until = $valid_until;
                        try{
                            $new_email_url->save();
                        }catch (\Database_Exception $e){}
                    }
                } else { //delete them all!
                    foreach ($email_urls as $url) {
                        try{
                            Model_Emailurl::query()->where('id', $url->id)->delete();
                        }catch (\Database_Exception $e){}
                    }
                }


                try{
                    $email->save();
                    Session::set_flash('success', e('Updated email #' . $id));
                    Response::redirect('admin/emails/view/'.$id);
                }catch (\Database_Exception $e){
                    Session::set_flash('error', e('Could not update email #' . $id));
                }
            }
        } else {
            if (Input::method() == 'POST') {
                $email->html_content = $val->validated('html_content');
                $email->subject = $val->validated('subject');
                $email->title = $val->validated('title');

                Session::set_flash('error', $val->error());
            }

            $this->template->set_global('email', $email, false);
        }

        $this->template->js = array(
            '/vendor/ckeditor/ckeditor.js',
            '/vendor/ckeditor/adapters/jquery.js',
            'emails/email-editor.js',
        );
        $this->template->title = "";
        $this->template->content = View::forge('admin/emails/edit', $data);

    }

    public function action_delete($id = null)
    {
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Email::find($id)) {
            try{
                $email->delete();
                Session::set_flash('success', e('Deleted email #' . $id));
            }catch (\Database_Exception $e){
                Session::set_flash('error', e('Could not delete email #' . $id));
            }
        }

        Response::redirect('admin/emails');

    }


    public function action_send($id = null)
    {
        if (!Auth::has_access('emails.send')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Email::find($id)) {
            \Package::load('email');

            foreach ($email->emailurls as $url) {
                if ($url->sent != 0) {
                    //skip it if already sent
                    continue;
                }

                //first get the address and stuff
                $doctoral = Model_Doctoral::find($url->doctoral_id);

                //check if he can be sent an email or else continue
                if(($doctoral->sendemail != 1) || ($doctoral->graduated != 0) || ($doctoral->active != 1)){
                    continue;
                }
                //create his address

                $unique_uri = Uri::create('preferences', array('ids' => Crypt::encode($url->doctoral_id . "|" . $id . "|" . $email->examperiod_id), 'token' => $url->token), array('cll' => ':ids', 'token' => ':token'));
                    //	echo $unique_uri; exit;
                $hours_left = $doctoral->hours_remaining;

                $new_p = "<p>Οι ώρες επιτήρησης που σας απομένουν είναι <b>" . $hours_left . "</b>.</p>
                <p><a href=\"$unique_uri\">Please click here to proceed and set your preferences.</a></p>
                <p>If you cannot see the url please copy the following string in your browser: $unique_uri</p>";
            //	echo $new_p; exit; 
	       //create the mail
                $email_to_send = Email::forge();

                //add all the doctoral's emails in an array and add his full name as value.
                $email_addresses_array = explode(",",$doctoral->email);
                foreach($email_addresses_array as $key){
                    $email_addresses_array[$key] = $doctoral->name . " " . $doctoral->surname;
                }

                //$email_to_send->to('sdi2200235@di.uoa.gr');         // TEST
                $email_to_send->to($email_addresses_array);

                $bcc_email = \Config::get('email.admin_bcc');
                    if (!empty($bcc_email)) {
                        $email_to_send->bcc($bcc_email);
                    }
                $email_to_send->subject($email->subject);
                $email_to_send->html_body($email->html_content . $new_p,true);
                $email_to_send->priority(\Email::P_HIGHEST);

                //die($email->html_content . $new_p);             // DEBUG

                //now try to send it!
                try {
                    $email_to_send->send();
                    $url->sent = 1;
                    try{
                        $url->save();
                        Session::set_flash('success', e('Sent email #' . $id));
                    }catch (\Database_Exception $e){
                        Session::set_flash('error', e('Could not save state but otherwise sent email #' . $id));
                    }

                } catch (\EmailValidationFailedException $e) {
                    Session::set_flash('error', e('The validation failed for email #' . $id . " " . $e->getMessage()));

                } catch (\EmailSendingFailedException $e) {
                    Session::set_flash('error', e('The driver could not send the email #' . $id . " " . $e->getMessage()));

                } catch (\SmtpConnectionException $e) {
                    Session::set_flash('error', e('The driver could not send the email #' . $id . " " . $e->getMessage()));

                }
                //$email_addresses = array_merge($email_addresses,array($doctoral->email => $doctoral->name." ".$doctoral->surname));
            }


        } else {
            Session::set_flash('error', e('Could not find email #' . $id));
        }

        Response::redirect('admin/emails/view/'.$id);


    }

    public function action_rearm($id = null)
    {
        if (!Auth::has_access('emails.rearm')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Email::find($id)) {
            $email_urls = $email->emailurls;
            //set timezone
            date_default_timezone_set(\Fuel\Core\Config::get('timezone'));
            //get validity time span
            $datespan = $email->datespan;
            //get today
            $today = date('Y-m-d h:i:s', time());
            //set max valid date
            $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $datespan days"));

            foreach ($email_urls as $url) {
                $url->sent = 0;
                $url->used = 0;
                $url->valid_until = $valid_until;
                try{
                    $url->save();
                }catch (\Database_Exception $e){}
            }
            try{
                $email->save();
                Session::set_flash('success', e('Rearmed email #' . $id));
            }catch (\Database_Exception $e){
                Session::set_flash('error', e('Could not rearm email #' . $id));
            }
            Response::redirect('admin/emails/view/'.$id);
        }

    }

    public function action_rearm_one($id = null)
    {
        if (!Auth::has_access('emails.rearm')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email_url = Model_Emailurl::find($id)){

            $doctoral = Model_Doctoral::find($email_url->doctoral_id);
            if($doctoral != null){
                $doctoral_string = $doctoral->name. " " .$doctoral->surname. " ". $doctoral->email;
            }else{
                $doctoral_string = "Something is wrong!";
            }

            try{
                $email = Model_Email::find($email_url->mail_id);
                $datespan = $email->datespan;

            }catch (\Exception $e){
                $datespan = 30;
                Session::set_flash('error', e('Error while rearming '.$doctoral_string." for email #" . $email_url->mail_id));
            }

            //set timezone
            date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

            //get today
            $today = date('Y-m-d h:i:s', time());
            //set max valid date
            $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $datespan days"));

            $email_url->sent = 0;
            $email_url->used = 0;
            $email_url->valid_until = $valid_until;
            try{
                $email_url->save();
                Session::set_flash('success', e('Rearmed '.$doctoral_string." for email #" . $email_url->mail_id));
            }catch (\Database_Exception $e){
                Session::set_flash('error', e('Could not rearm '.$doctoral_string." for email #" . $email_url->mail_id));
            }
            Response::redirect('admin/emails/view/'.$email_url->mail_id);

        }


    }
    public function action_delete_one($id = null)
    {
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email_url = Model_Emailurl::find($id)){

            $doctoral = Model_Doctoral::find($email_url->doctoral_id);
            if($doctoral != null){
                $doctoral_string = $doctoral->name. " " .$doctoral->surname. " ". $doctoral->email;
            }else{
                $doctoral_string = "Something is wrong!";
            }

            $email_id = $email_url->mail_id;
            try{
                $email_url->delete();
                Session::set_flash('success', e('Deleted '.$doctoral_string." for email #" .  $email_id));
            }catch (\Database_Exception $e){
                Session::set_flash('error', e('Could not delete '.$doctoral_string." for email #" .  $email_id));
            }
            Response::redirect('admin/emails/view/'. $email_id);

        }

    }
    public function delete_selected($email_id,$checkbox_urls){
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        foreach($checkbox_urls as $u){
            $email_url = Model_Emailurl::find($u);
            if(is_null($email_url)){
                continue;
            }
            try{
                $email_url->delete();
            }catch (\Database_Exception $e){
            }
        }
        Session::set_flash('success', e('Deleted selected!'));
        Response::redirect('admin/emails/view/'.$email_id);

    }

    public function rearm_selected($email_id,$checkbox_urls){
        if (!Auth::has_access('emails.rearm')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        //set timezone
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

        //get validity time span
        try{
            $email = Model_Email::find($email_id);
            $datespan = $email->datespan;

        }catch (\Exception $e){
            $datespan = 30;
            Session::set_flash('error', e("Minor datespan error while rearming email #" . $email_id." datespan automatically set to 30 days."));
        }

        //get today
        $today = date('Y-m-d h:i:s', time());
        //set max valid date
        $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $datespan days"));

        foreach($checkbox_urls as $u){
            $email_url = Model_Emailurl::find($u);
            if(is_null($email_url)){
                continue;
            }
            $email_url->sent = 0;
            $email_url->used = 0;
            $email_url->valid_until = $valid_until;
            try{
                $email_url->save();
            }catch (\Database_Exception $e){
            }
        }
        Session::set_flash('success', e('Rearmed selected!'));
        Response::redirect('admin/emails/view/'.$email_id);
    }
}
