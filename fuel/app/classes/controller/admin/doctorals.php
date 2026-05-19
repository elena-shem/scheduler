<?php

class Controller_Admin_Doctorals extends Controller_Admin
{

    public function action_index()
    {
        if (!Auth::has_access('doctorals.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $data['doctorals'] = Model_Doctoral::find('all', array(
            'where' => array(
                array('deleted_at', '=', null),
                array('graduated', '=', 0),
            ),

            'order_by' => array('surname' => 'asc'),
            'related'  => array('professors'),
        ));


        $this->template->title = "Active Doctorals";

        $this->template->js = array(
            'vendor/sortable.js',
            'doctorals/doctorals-index.js'
        );
        $this->template->css = array(
            'doctorals.css',
        );
        $this->template->content = View::forge('admin/doctorals/index', $data);

    }

    public function action_view($id = null)
    {
        if (!Auth::has_access('doctorals.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/doctorals');
        }
        $data['doctoral'] = Model_Doctoral::find($id, array(
            'related' => array('supervisions', 'supervisions.examcourse', 'professors')
        ));
        if (!$data['doctoral'] || $data['doctoral']->deleted_at !== null) {
            Session::set_flash('error', e('Doctoral not found.'));
            return Response::redirect('admin/doctorals');
        }
        $data['professors'] = $data['doctoral']->professors;
        $this->template->js = array(
            'doctorals/doctorals-view.js'
        );
        $this->template->css = array(
            'table-view.css'
        );
        $this->template->title = "";
        $this->template->content = View::forge('admin/doctorals/view', $data);

    }

public function action_create()
{
    if (!Auth::has_access('doctorals.create')) {
        Session::set_flash('error', e('You do not have access to this function!'));
        Response::redirect('admin/doctorals');
    }

    // 1. Сначала подготавливаем данные, которые НУЖНЫ форме в любом случае (GET или POST)
    $data['professors'] = array();
    $professors_models = Model_Professor::find('all');
    foreach ($professors_models as $professor) {
        $data['professors'][$professor->id] = $professor->surname . " " . $professor->name;
    }
    
    // При создании список ID "обладаемых" профессоров пуст
    $data['possesed_professors'] = array(); 
    $data['type'] = 'create';

    // 2. Обработка POST запроса
    if (Input::method() == 'POST' && Security::check_token()) {
        $val = Model_Doctoral::validate('create');

        if ($val->run()) {
            $doctoral = Model_Doctoral::forge(array(
                'name' => Input::post('name'),
                'surname' => Input::post('surname'),
                'email' => preg_replace('/\s+/', '', Input::post('email')),
                'am' => Input::post('am'),
                'registrationdate' => Input::post('registrationdate'),
                'telephone' => Input::post('telephone'),
                'sendemail' => Input::post('sendemail'),
                'suspended' => Input::post('suspended'),
                'comment' => Input::post('comment'),
                'hours_remaining' => Input::post('hours_remaining'),
                'hours_completed' => Input::post('hours_completed'),
                'max_assignments' => Input::post('max_assignments'),
                'bonus_weight' => Input::post('bonus_weight'),
                'active' => 1,
                'graduated' => 0,
            ));

            if ($doctoral and $doctoral->save()) {
                $new_professors = Input::post('professors');
                if (is_array($new_professors)) {
                    foreach ($new_professors as $prof_id) {
                        $props = array('professor_id' => $prof_id, 'doctoral_id' => $doctoral->id);
                        $new_Association = new Model_Doctoralsupervisor($props);
                        $new_Association->save();
                    }
                }
                
                Session::set_flash('success', e('Added doctoral #' . $doctoral->id . '.'));
                Response::redirect('admin/doctorals');
            } else {
                Session::set_flash('error', e('Could not save doctoral!'));
            }
        } else {
            Session::set_flash('error', $val->error());
            // Если валидация не прошла, данные из Input::post автоматически подставятся в форму
            // благодаря вашим isset($doctoral) проверкам во View
        }
    }

    // 3. Рендерим страницу. Переменная $data гарантированно содержит 'possesed_professors'
    $this->template->title = "";
    $this->template->content = View::forge('admin/doctorals/create', $data);
}

    public function action_edit($id = null)
    {
        if (!Auth::has_access('doctorals.update')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/doctorals');
        }

        $doctoral = Model_Doctoral::find($id);
        $val = Model_Doctoral::validate('edit');

        $data['professors'] = array();
        $data['possesed_professors'] = array();
        foreach ($doctoral->professors as $professor) {
            $data['possesed_professors'][$professor->id] = $professor->id;
        }
        $professors = Model_Professor::find('all');
        foreach ($professors as $professor) {
            $data['professors'][$professor->id] = $professor->surname." ".$professor->name;
        }

        if ($val->run()) {

            $doctoral->name = Input::post('name');
            $doctoral->surname = Input::post('surname');
            $doctoral->email = preg_replace('/\s+/', '', Input::post('email'));
            $doctoral->am = Input::post('am');
            $doctoral->registrationdate = Input::post('registrationdate');
            $doctoral->telephone = Input::post('telephone');
            $doctoral->sendemail = Input::post('sendemail');
            $doctoral->suspended = Input::post('suspended');
            $doctoral->comment = Input::post('comment');
            $doctoral->hours_remaining = Input::post('hours_remaining');
            $doctoral->hours_completed = Input::post('hours_completed');
            $doctoral->max_assignments = Input::post('max_assignments');
            $doctoral->bonus_weight = Input::post('bonus_weight');

            //Find all the old professors and delete them
            Model_Doctoralsupervisor::query()->where('doctoral_id', $id)->delete();
            //add the new ones
            $new_professors = Input::post('professors');
            if (is_array($new_professors)) {
                foreach ($new_professors as $prof_id) {
                    $props = array('professor_id' => $prof_id, 'doctoral_id' => $id);
                    $new_Association = new Model_Doctoralsupervisor($props);
                    $new_Association->save();
                }
            }

            if ($doctoral->save()) {
                Session::set_flash('success', e('Updated doctoral #' . $id));

                Response::redirect('admin/doctorals');
            } else {
                Session::set_flash('error', e('Could not update doctoral #' . $id));
            }

        } else {
            if (Input::method() == 'POST') {
                $doctoral->name = $val->validated('name');
                $doctoral->surname = $val->validated('surname');
                $doctoral->email = $val->validated('email');
                $doctoral->am = $val->validated('am');
                $doctoral->registrationdate = $val->validated('registrationdate');
                $doctoral->telephone = $val->validated('telephone');
                $doctoral->sendemail = $val->validated('sendemail');
                $doctoral->suspended = $val->validated('suspended');
                $doctoral->comment = $val->validated('comment');
                $doctoral->hours_remaining = $val->validated('hours_remaining');
                $doctoral->hours_completed = $val->validated('hours_completed');
                $doctoral->max_assignments = $val->validated('max_assignments');
                $doctoral->bonus_weight = $val->validated('bonus_weight');

                Session::set_flash('error', $val->error());
            }

            $this->template->set_global('doctoral', $doctoral, false);
        }
        $this->template->js = array(
            'doctorals/doctorals-edit.js'
        );

        $this->template->title = "";
        $this->template->content = View::forge('admin/doctorals/edit',$data);

    }

public function action_delete($id = null)
{
    if (!Auth::has_access('doctorals.delete')) {
        Session::set_flash('error', e('You do not have access to this function!'));
        return Response::redirect('admin/doctorals');
    }

    if ($doctoral = Model_Doctoral::find($id)) {

        // soft delete
        $doctoral->deleted_at = date('Y-m-d H:i:s');
        $doctoral->save();

        Session::set_flash('success', e('Doctoral moved to deleted #' . $id));
    } else {
        Session::set_flash('error', e('Could not delete doctoral #' . $id));
    }

    return Response::redirect('admin/doctorals');
}

public function action_deleted()
{
    $data['doctorals'] = Model_Doctoral::find('all', array(
        'where' => array(
            array('deleted_at', '!=', null),
        ),
        'order_by' => array('surname' => 'asc'),
        'related'  => array('professors'),
    ));

    $this->template->title = "Deleted Doctorals";
    $this->template->css = array('doctorals.css');
    $this->template->js  = array('vendor/sortable.js', 'doctorals/doctorals-index.js');
    $this->template->content = View::forge('admin/doctorals/deleted', $data);
}


public function action_restore($id = null)
{
    if (!Auth::has_access('doctorals.update')) {
        Session::set_flash('error', e('You do not have access to this function!'));
        return Response::redirect('admin/doctorals');
    }

    if ($doctoral = Model_Doctoral::find($id)) {
        $doctoral->deleted_at = null;
        $doctoral->save();
        Session::set_flash('success', e('Doctoral restored #' . $id));
    } else {
        Session::set_flash('error', e('Could not restore doctoral #' . $id));
    }

    return Response::redirect('admin/doctorals/deleted');
}

public function action_graduated()
{
    $data['doctorals'] = Model_Doctoral::find('all', array(
        'where' => array(
            array('deleted_at', '=', null),
            array('graduated', '=', 1),
        ),
        'order_by' => array('surname' => 'asc'),
        'related'  => array('professors'),
    ));

    $this->template->title = "Graduated Doctorals";
    $this->template->css = array('doctorals.css');
    $this->template->js  = array('vendor/sortable.js', 'doctorals/doctorals-index.js');
    $this->template->content = View::forge('admin/doctorals/graduated', $data);
}


public function action_not_responded()
    {
        if (!Auth::has_access('doctorals.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $doctorals = Model_Doctoral::find('all', array(
            'where'    => array(
                array('deleted_at', '=', null),
                array('graduated', '=', 0),
            ),
            'related'  => array('emailurls', 'professors'),
            'order_by' => array('surname' => 'asc'),
        ));

        $not_responded = array();

        foreach ($doctorals as $doc) {
            $last_email = null;

            if (!empty($doc->emailurls)) {
                foreach ($doc->emailurls as $emailurl) {
                    if ($emailurl->sent) {
                        if (!$last_email || $emailurl->created_at > $last_email->created_at) {
                            $last_email = $emailurl;
                        }
                    }
                }
            }

            if (!$last_email || !$last_email->used) {
                $not_responded[] = $doc;
            }
        }

        // Группировка списка по преподавателям для View
        $professors_list = array();
        foreach ($not_responded as $doc) {
            if (!empty($doc->professors)) {
                foreach ($doc->professors as $prof) {
                    $prof_id = $prof->id;
                    if (!isset($professors_list[$prof_id])) {
                        $professors_list[$prof_id] = array(
                            'professor' => $prof,
                            'doctorals' => array()
                        );
                    }
                    $professors_list[$prof_id]['doctorals'][] = $doc;
                }
            }
        }

        $data['professors_list'] = $professors_list;

        $this->template->title   = "Supervisors of Non-Responding Doctorals";
        $this->template->css     = array('doctorals.css');
        $this->template->js      = array('vendor/sortable.js', 'doctorals/doctorals-index.js');
        $this->template->content = View::forge('admin/doctorals/not_responded', $data);
    }

    public function action_notify_single_supervisor($prof_id = null)
    {
        if (!Auth::has_access('doctorals.update')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            return Response::redirect('admin/doctorals/not_responded');
        }

        if (!$prof_id || !$professor = Model_Professor::find($prof_id, array('related' => array('doctorals', 'doctorals.emailurls')))) {
            Session::set_flash('error', 'Professor not found');
            return Response::redirect('admin/doctorals/not_responded');
        }

        if ( ! \Package::loaded('email')) {
            \Package::load('email');
        }

        $not_responded = array();
        foreach ($professor->doctorals as $doc) {
            if ($doc->deleted_at !== null || $doc->graduated == 1) continue;

            $last_email = null;
            if (!empty($doc->emailurls)) {
                foreach ($doc->emailurls as $emailurl) {
                    if ($emailurl->sent) {
                        if (!$last_email || $emailurl->created_at > $last_email->created_at) {
                            $last_email = $emailurl;
                        }
                    }
                }
            }

            if (!$last_email || !$last_email->used) {
                $not_responded[] = $doc;
            }
        }

        if (empty($not_responded)) {
            Session::set_flash('success', 'All students of this professor have responded.');
            return Response::redirect('admin/doctorals/not_responded');
        }

        try {
            $body = "Notification for supervising professor {$professor->surname} {$professor->name}:\n\n" .
                    "The following doctoral students have not responded to the latest request:\n\n";

            foreach ($not_responded as $doc) {
                $body .= "  - Name: {$doc->surname} {$doc->name}\n" .
                         "  - ID: {$doc->id}\n" .
                         "  - Email: {$doc->email}\n\n";
            }

            $body .= "Please review the situation or contact the students if necessary.\n\n" .
                     "Generated automatically by DI Exam Scheduler.";



                $email = \Email::forge();
                $email->to($professor->email);
                $email->subject('Doctoral students pending response');
                $email->body($body);

                $bcc_email = \Config::get('email.admin_bcc');
                    if (!empty($bcc_email)) {
                        $email->bcc($bcc_email);
                    }
                                    
                $email->send();


            Session::set_flash('success', "Notification sent to {$professor->surname} {$professor->name}.");

        } catch (\Exception $e) {
            Session::set_flash('error', 'Unexpected error while sending email: '.$e->getMessage());
        }

        return Response::redirect('admin/doctorals/not_responded');
    }

   public function action_notify_all_supervisors()
{
    set_time_limit(0);

    if (!Auth::has_access('doctorals.update')) {
        Session::set_flash('error', e('You do not have access to this function!'));
        return Response::redirect('admin/doctorals/not_responded');
    }

    if (Input::method() !== 'POST' || !\Security::check_token()) {
        Session::set_flash('error', 'Invalid request.');
        return Response::redirect('admin/doctorals/not_responded');
    }

    if (!\Package::loaded('email')) {
        \Package::load('email');
    }

    // Поиск докторантов
    $doctorals = Model_Doctoral::find('all', array(
        'related'  => array('emailurls', 'professors'),
    ));

    $not_responded = array();
    foreach ($doctorals as $doc) {
        $last_email = null;
        if (!empty($doc->emailurls)) {
            foreach ($doc->emailurls as $emailurl) {
                if ($emailurl->sent) {
                    if (!$last_email || $emailurl->created_at > $last_email->created_at) {
                        $last_email = $emailurl;
                    }
                }
            }
        }
        if (!$last_email || !$last_email->used) {
            $not_responded[] = $doc;
        }
    }

    if (empty($not_responded)) {
        Session::set_flash('success', 'There are no doctoral students to notify.');
        return Response::redirect('admin/doctorals/not_responded');
    }

    // Группировка
    $professors_to_notify = array();
    foreach ($not_responded as $doctoral) {
        if (empty($doctoral->professors)) continue;
        foreach ($doctoral->professors as $professor) {
            $prof_id = $professor->id;
            if (!isset($professors_to_notify[$prof_id])) {
                $professors_to_notify[$prof_id] = array(
                    'professor' => $professor,
                    'doctorals' => array()
                );
            }
            $professors_to_notify[$prof_id]['doctorals'][] = $doctoral;
        }
    }

    $success_count = 0;
    $error_count = 0;

    foreach ($professors_to_notify as $data) {
        $professor = $data['professor'];
        
        $body = "Notification for supervising professor {$professor->surname} {$professor->name}:\n\n" .
                "The following doctoral students have not responded to the latest request:\n\n";

        foreach ($data['doctorals'] as $doc) {
            $body .= "  - Name: {$doc->surname} {$doc->name}\n" .
                     "  - ID: {$doc->id}\n" .
                     "  - Email: {$doc->email}\n\n";
        }

        $body .= "Generated automatically by DI Exam Scheduler.";

        $max_retries = 3;
        $sent = false;

        for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
            try {
                $email = \Email::forge();
                $email->to($professor->email);
                $email->subject('Doctoral students pending response');
                $email->body($body);
                
                $bcc_email = \Config::get('email.admin_bcc');
                    if (!empty($bcc_email)) {
                        $email->bcc($bcc_email);
                    }

                $email->send();

                $success_count++;
                $sent = true;
                
                usleep(1500000); 
                break;

            } catch (\Exception $e) {
                if ($attempt < $max_retries) {
                    // Если сервер отклонил запрос, ждем 3 секунды и пробуем снова
                    sleep(3);
                } else {
                    $error_count++;
                    \Log::error('Mail fail for prof ID '.$professor->id.': '.$e->getMessage());
                }
            }
        }
    }

    if ($error_count > 0) {
        Session::set_flash('error', "Processed: {$success_count} sent, {$error_count} failed. Check logs for details.");
    } else {
        Session::set_flash('success', "All {$success_count} notifications were sent successfully.");
    }

    return Response::redirect('admin/doctorals/not_responded');
}

}

