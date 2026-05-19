<?php
class Controller_Admin_Professors extends Controller_Admin{

	public function action_index()
	{
        if (!Auth::has_access('professors.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin'); return;
        }

		$data['professors'] = Model_Professor::find('all');

		$this->template->title = "Active Professors";
        $this->template->js = array(
            'vendor/sortable.js',
            'professors/professors-index.js'
        );
		$this->template->content = View::forge('admin/professors/index', $data);

	}

	public function action_view($id = null)
	{
        if (!Auth::has_access('professors.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }
		$prof = $data['professor'] = Model_Professor::find($id);
        if (!$prof) {
            Session::set_flash('error', e('Professor not found.'));
            Response::redirect('admin/professors'); return;
        }
        $data['courses'] = $prof->courses;
        $data['doctorals'] = $prof->doctorals;

        $this->template->js = array(
            'professors/professors-view.js'
        );
        $this->template->css = array(
            'table-view.css'
        );
		$this->template->title = "";
		$this->template->content = View::forge('admin/professors/view', $data);

	}

	public function action_create()
	{
        if (!Auth::has_access('professors.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }

		if (Input::method() == 'POST' && Security::check_token())
		{
			$val = Model_Professor::validate('create');

			if ($val->run())
			{
				$professor = Model_Professor::forge(array(
					'name' => Input::post('name'),
					'surname' => Input::post('surname'),
					'email' => Input::post('email'),
                    'telephone' => Input::post('telephone'),
                    'office' => Input::post('office'),
				));

				if ($professor and $professor->save())
				{
					Session::set_flash('success', e('Added professor #'.$professor->id.'.'));

					Response::redirect('admin/professors'); return;
				}

				else
				{
					Session::set_flash('error', e('Could not save professor.'));
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "";
		$this->template->content = View::forge('admin/professors/create');

	}

    public function action_edit($id = null)
    {
        if (!Auth::has_access('professors.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }

        $data['doctorals'] = array();
        $data['possesed_doctorals'] = array();

        $data['courses'] = array();
        $data['possesed_courses'] = array();

        $professor = Model_Professor::find($id);
        if (!$professor) {
            Session::set_flash('error', e('Professor not found.'));
            Response::redirect('admin/professors'); return;
        }

        foreach ($professor->doctorals as $doctoral){
            $data['possesed_doctorals'][$doctoral->id] = $doctoral->id;
        }

        $doctorals = Model_Doctoral::find('all', array(
            'where' => array(array('active', 1)),
        ));
        foreach ($doctorals as $doctoral){
            $data['doctorals'][$doctoral->id] = $doctoral->surname." ".$doctoral->name;
        }


        foreach ($professor->courses as $course){
            $data['possesed_courses'][$course->id] = $course->id;
        }

        $courses = Model_Course::find('all');
        foreach ($courses as $course){
            if($course->title == 'DUMMY' && $course->code == 'DUM') continue;
            $data['courses'][$course->id] = $course->code ." ".$course->title;
        }

        $val = Model_Professor::validate('edit');

        if (Input::method() == 'POST' && Security::check_token())
        {
            if ($val->run())
            {
                $professor->name = Input::post('name');
                $professor->surname = Input::post('surname');
                $professor->email = Input::post('email');
                $professor->telephone = Input::post('telephone');
                $professor->office = Input::post('office');

                if ($professor->save())
                {
                    Model_Doctoralsupervisor::query()
                        ->where('professor_id', $professor->id)
                        ->delete();

                    $new_doctorals = Input::post('doctorals', array());
                    if (is_array($new_doctorals)) {
                        foreach ($new_doctorals as $doc_id) {
                            (new Model_Doctoralsupervisor(array(
                                'doctoral_id'  => $doc_id,
                                'professor_id' => $professor->id,
                            )))->save();
                        }
                    }

                    Model_Professorcourse::query()
                        ->where('professor_id', $professor->id)
                        ->delete();

                    $new_courses = Input::post('courses', array());
                    if (is_array($new_courses)) {
                        foreach ($new_courses as $course_id) {
                            (new Model_Professorcourse(array(
                                'course_id'    => $course_id,
                                'professor_id' => $professor->id,
                            )))->save();
                        }
                    }

                    Session::set_flash('success', e('Updated professor #' . $professor->id));
                    Response::redirect('admin/professors'); return;
                }
                else
                {
                    Session::set_flash('error', e('Could not update professor #' . $professor->id));
                }
            }
            else
            {
                $professor->name = $val->validated('name');
                $professor->surname = $val->validated('surname');
                $professor->email = $val->validated('email');
                $professor->telephone = Input::post('telephone');
                $professor->office = Input::post('office');

                Session::set_flash('error', $val->error());
            }
        }

        $this->template->set_global('professor', $professor, false);
        $this->template->title = "";
        $this->template->content = View::forge('admin/professors/edit', $data);
    }

    public function action_delete($id = null)
    {
        if (!Auth::has_access('professors.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }

        $professor = Model_Professor::find_with_deleted($id);

        if ($professor)
        {
            if ($professor->deleted_at !== null) {
                Session::set_flash('error', e('Professor already deleted.'));
                Response::redirect('admin/professors'); return;
            }

            $professor->soft_delete();
            Session::set_flash('success', e('Deleted professor #'.$id));
        }
        else
        {
            Session::set_flash('error', e('Could not delete professor #'.$id));
        }

        Response::redirect('admin/professors'); return;
    }

    public function action_deleted()
    {
        if (!Auth::has_access('professors.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }

        $data['professors'] = Model_Professor::find_deleted();

        $this->template->title = "Deleted Professors";
        $this->template->content = View::forge('admin/professors/deleted', $data);
    }

    public function action_restore($id = null)
    {
        if (!Auth::has_access('professors.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/professors'); return;
        }

        $professor = Model_Professor::find_with_deleted($id);
        if (!$professor) {
            Session::set_flash('error', e('Professor not found.'));
            Response::redirect('admin/professors/deleted'); return;
        }

        if ($professor->deleted_at === null) {
            Session::set_flash('error', e('Professor is not deleted.'));
            Response::redirect('admin/professors/deleted'); return;
        }

        $professor->restore();
        Session::set_flash('success', e('Restored professor #'.$id));
        Response::redirect('admin/professors/deleted'); return;
    }


}