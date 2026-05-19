<?php

class Controller_Admin_Custom_Emails extends Controller_Admin
{

    public function action_index()
    {
        if (!Auth::has_access('emails.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        $data['emails'] = Model_Custom_Email::find('all',array('order_by' => array('id' => 'desc')));
        $this->template->title = "Custom Emails";
        $this->template->content = View::forge('admin/custom/emails/index', $data);

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

        $email = $data['email'] = Model_Custom_Email::find($id);
        $question = Model_Custom_Question::find($email->question_id);
        if($question == null){
            $data['question_info'] = "None selected!";
        }else{
            $data['question_info'] = $question->title;
        }

        $once = false;
        $data['doctorals'] = array();
        $data['emails_number'] = 0;
        $data['emails_used'] = 0;
        $data['emails_saved'] = 0;

        foreach ($email->custom_emailurls as $url) {
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
            $obj->custom_comment = $url->answer."<br> textbox1: ".($url->logical_1?'YES':'NO')."<br> textbox2: ".($url->logical_2?'YES':'NO');
            if($tmp_now > $tmp_valid_until){
                $obj->is_still_valid = false;
            }
            $obj->valid_until = date( 'd/m/Y H:i',$tmp_valid_until);

            $obj->updated_at = date( 'd/m/Y H:i',$url->updated_at);

            $c_url_data = Model_Custom_Emailurl::query()->where('doctoral_id','=',$url->doctoral_id)->where('mail_id','=',$email->id)->get_one();

            $obj->has_saved_settings = (is_null($c_url_data->answer) && is_null($c_url_data->logical_1) && is_null($c_url_data->logical_2) )?false:true;
            if($obj->has_saved_settings){
                $data['emails_saved']+=1;
            }
            //create unique url
            $obj->unique_uri = Uri::create('questionnaire', array('ids' => Crypt::encode($obj->id . "|" . $email->id . "|" . $email->question_id), 'token' => $url->token), array('cll' => ':ids', 'token' => ':token'));

            array_push($data['doctorals'], $obj);
        }

        $this->template->js = array(
            'vendor/sortable.js',
            'questions/email-view.js',

        );
        $this->template->css = array(
            'email-view.css',
        );
        $this->template->title = "";
        $this->template->content = View::forge('admin/custom/emails/view', $data, false);

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


        $data['questions'] = array();
        $data['already_selected_question'] = 'none';
        $questions = Model_Custom_Question::find('all');
        $data['questions'][-1] = '';
        foreach($questions as $q){
            $data['questions'][$q->id] = $q->title;
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
            $val = Model_Custom_Email::validate('create');


            if ($val->run()) {
                $email = Model_Custom_Email::forge(array(
                    'question_id' => Input::post('question_id'),
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
                                $new_email_url = new Model_Custom_Emailurl();
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

                        Response::redirect('admin/custom/emails/view/'.$email->id);


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
        $this->template->content = View::forge('admin/custom/emails/create', $data);

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


        $email = Model_Custom_Email::find($id);

        $email_urls = $email->custom_emailurls;


        $data['already_selected_question'] = $email->question_id;
        $data['questions'] = array();
        $data['questions'][-1] = '';
        $questions = Model_Custom_Question::find('all');
        foreach($questions as $q){
            $data['questions'][$q->id] = $q->title;
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
        $val = Model_Custom_Email::validate('edit');

        if ($val->run()) {
            if (Input::method() == 'POST' && Security::check_token()) {
                $email->html_content = $html_content;
                $email->subject = Input::post('subject');
                $email->title = Input::post('title');
                $email->question_id = Input::post('question_id');
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
                                Model_Custom_Emailurl::query()->where('id', $url->id)->delete();
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
                        $new_email_url = new Model_Custom_Emailurl();
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
                            Model_Custom_Emailurl::query()->where('id', $url->id)->delete();
                        }catch (\Database_Exception $e){}
                    }
                }


                try{
                    $email->save();
                    Session::set_flash('success', e('Updated email #' . $id));
                    Response::redirect('admin/custom/emails/view/'.$id);
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
        $this->template->content = View::forge('admin/custom/emails/edit', $data);

    }

    public function action_delete($id = null)
    {
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Custom_Email::find($id)) {
            try{
                $email->delete();
                Session::set_flash('success', e('Deleted email #' . $id));
            }catch (\Database_Exception $e){
                Session::set_flash('error', e('Could not delete email #' . $id));
            }
        }

        Response::redirect('admin/custom/emails');

    }


    public function action_send($id = null)
    {
        if (!Auth::has_access('emails.send')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Custom_Email::find($id)) {
            \Package::load('email');

            foreach ($email->custom_emailurls as $url) {
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

                $unique_uri = Uri::create('questionnaire', array('ids' => Crypt::encode($url->doctoral_id . "|" . $id . "|" . $email->question_id), 'token' => $url->token), array('cll' => ':ids', 'token' => ':token'));
                $new_p = "<p><a href=\" $unique_uri \">Please click here to proceed and set your preferences.</a></p>
                <p>If you cannot see the url please copy the following string in your browser: $unique_uri</p>";
                //create the mail
                $email_to_send = Email::forge();

                //add all the doctoral's emails in an array and add his full name as value.
                $email_addresses_array = explode(",",$doctoral->email);
                foreach($email_addresses_array as $key){
                    $email_addresses_array[$key] = $doctoral->name . " " . $doctoral->surname;
                }
                $email_to_send->to($email_addresses_array);
                $bcc_email = \Config::get('email.admin_bcc');                  
                if (!empty($bcc_email)) {
                    $email_to_send->bcc($bcc_email);
                }
		        
                $email_to_send->subject($email->subject);
                $email_to_send->html_body($email->html_content . $new_p,true);
                $email_to_send->priority(\Email::P_HIGHEST);

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

        Response::redirect('admin/custom/emails/view/'.$id);


    }

    public function action_rearm($id = null)
    {
        if (!Auth::has_access('emails.rearm')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        if ($email = Model_Custom_Email::find($id)) {
            $email_urls = $email->custom_emailurls;
            //set timezone
            date_default_timezone_set(\Fuel\Core\Config::get('timezone'));
            //get validity time span
            $datespan = \Fuel\Core\Config::get('email_validity_day_span');
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
            Response::redirect('admin/custom/emails/view/'.$id);
        }

    }




    public function delete_selected($email_id,$checkbox_urls){
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        foreach($checkbox_urls as $u){
            $email_url = Model_Custom_Emailurl::find($u);
            if(is_null($email_url)){
                continue;
            }
            try{
                $email_url->delete();
            }catch (\Database_Exception $e){
            }
        }
        Session::set_flash('success', e('Deleted selected!'));
        Response::redirect('admin/custom/emails/view/'.$email_id);

    }

    public function rearm_selected($email_id,$checkbox_urls){
        if (!Auth::has_access('emails.rearm')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        //set timezone
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));
        //get validity time span
        $datespan = \Fuel\Core\Config::get('email_validity_day_span');
        //get today
        $today = date('Y-m-d h:i:s', time());
        //set max valid date
        $valid_until = date('Y-m-d h:i:s', strtotime($today . " + $datespan days"));

        foreach($checkbox_urls as $u){
            $email_url = Model_Custom_Emailurl::find($u);
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
        Response::redirect('admin/custom/emails/view/'.$email_id);
    }
}
