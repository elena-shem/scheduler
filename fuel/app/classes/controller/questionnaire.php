<?php
class Controller_Questionnaire extends Controller_Template
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
        $question_id = $id_pieces[2];
        $hash = Input::get('token');
        $email_url = Model_Custom_Emailurl::query()->where('doctoral_id', $doctoral_id)->where('mail_id', $email_id)->get_one();

        //get today
        $today = date('Y-m-d h:i:s', time());

        //check if links are open through global variable
        $globalsettings = Model_Globalsetting::find('last');

        if (
            ((!is_null($email_url)) && ($hash === $email_url->token) && (($email_url->valid_until >= $today ) || (1 == $globalsettings->overridelinkexpirationdate && $globalsettings->linkexpirationdate >= $today)))
            ||
            ((!is_null($email_url)) && ($hash === $email_url->token) && (1 == $globalsettings->alllinksopen))

        ) {
            // Url Is Valid
            // use it
            $email_url->used = 1;
            $email_url->save();
            //proceed
            //$doctoral = Model_Doctoral::find($doctoral_id);
            
            
            //do da stuff here
            //$data = "";
            
            $this->do_stuff($doctoral_id, $question_id, $email_url);

            
            
            //$this->template->title = "Welcome $doctoral->name !";
            //$this->template->content = View::forge('preferences/index', $data);
            //$this->template->css = array('preferences.css');
            //$this->template->js = array('preferences.js');

        } else {
            // Incorrect Url
            Response::redirect('welcome');
        }
    }
    
    
    
    public static function getUrl()
    {
        $url = 'questionnaire';
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
    
    
    
    private function do_stuff($doctoral_id, $question_id, $email_url)
    {
        $question = Model_Custom_Question::find($question_id);
        if($question == null)
        {
                        
            Session::set_flash('error', 'The requested questionnaire does not exist');
            Response::redirect('welcome');
        }
        
        $doctoral = Model_Doctoral::find($doctoral_id);
        if($doctoral == null)
        {
            // The doctoral id given doesn't correspond to a `doctorals` id
            // in the database.
            
            Session::set_flash('error', 'The requested doctoral does not exist');
            Response::redirect('welcome');
        }



        if(Input::method() == 'POST')
        {


            $email_url->answer = \Fuel\Core\Security::xss_clean(htmlspecialchars(strip_tags(Input::post('answer'))));
            $email_url->logical_1 = Input::post('logical1');
            $email_url->logical_2 = Input::post('logical2');

            try{
               $email_url->save();
            }catch(exception $e){
                Session::set_flash('success', e('Οι προτιμήσεις σας ΔΕΝ αποθηκεύτηκαν! Συνέβη σφάλμα! Επικοινωνήστε με email.'));
                Response::redirect(static::getUrl());

            }
            Session::set_flash('success', e('Οι προτιμήσεις σας αποθηκεύτηκαν επιτυχώς'));
            Response::redirect(static::getUrl());
        }
        
        $data['answer'] = $email_url->answer;
        $data['logical_1'] = $email_url->logical_1;
        $data['logical_2'] = $email_url->logical_2;

        $data['q_title'] = $question->title;
        $data['question_html'] = $question->question_html;
        
        $this->template->title = "Welcome $doctoral->name !";
        $this->template->css = array('preferences.css');
        $this->template->js = array();
        $this->template->content = View::forge('questionnaire/index', $data,false);
    }
    
}

