<?php
class Controller_Admin_Courses extends Controller_Admin{

	public function action_index()
	{
        if (!Auth::has_access('courses.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin'); return;
        }
		$data['courses'] = Model_Course::find('all');
		$this->template->title = "Active Courses";
		$this->template->js = array(
			'vendor/sortable.js',
			'courses/courses-index.js'
		);
		$this->template->content = View::forge('admin/courses/index', $data);

	}

	public function action_view($id = null)
	{
        if (!Auth::has_access('courses.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/courses'); return;
        }

		$course = $data['course'] = Model_Course::find($id);
		if (!$course) {
			Session::set_flash('error', e('Course not found.'));
			Response::redirect('admin/courses'); return;
		}
		$data['professors'] = $course->professors;

        $this->template->css = array(
            'table-view.css'
        );

		$this->template->js = array(
			'courses/courses-view.js'
		);
        $this->template->title = "";
		$this->template->content = View::forge('admin/courses/view', $data);

	}

	public function action_create()
	{
        if (!Auth::has_access('courses.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/courses'); return;
        }

		if (Input::method() == 'POST')
		{
			$val = Model_Course::validate('create');

			if ($val->run())
			{
				$course = Model_Course::forge(array(
					'special_id' => Input::post('special_id'),
					'code' => Input::post('code'),
					'code2' => Input::post('code2'),
					'title' => Input::post('title'),
					'number_of_supervisors_winter' => Input::post('number_of_supervisors_winter'),
					'number_of_supervisors_summer' => Input::post('number_of_supervisors_summer'),
					'number_of_supervisors_september' => Input::post('number_of_supervisors_september'),
				));

				if ($course and $course->save())
				{
					Session::set_flash('success', e('Added course #'.$course->id.'.'));

					Response::redirect('admin/courses'); return;
				}

				else
				{
					Session::set_flash('error', e('Could not save course.'));
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "";
		$this->template->content = View::forge('admin/courses/create');

	}

	public function action_edit($id = null)
	{
		if (!Auth::has_access('courses.update')){
			Session::set_flash('error', e('You do not have access to this function!'));
			Response::redirect('admin/courses'); return;
		}

		$course = Model_Course::find($id);
		if (!$course) {
			Session::set_flash('error', e('Course not found.'));
			Response::redirect('admin/courses'); return;
		}

		$val = Model_Course::validate('edit');

		$data['professors'] = array();
		$data['possesed_professors'] = array();

		foreach ($course->professors as $professor) {
			$data['possesed_professors'][$professor->id] = $professor->id;
		}

		$professors = Model_Professor::find('all');
		foreach ($professors as $professor) {
			$data['professors'][$professor->id] = $professor->surname . " " . $professor->name;
		}

		if (Input::method() == 'POST')
		{
			if ($val->run())
			{
				if ($course->code == 'DUM' && Input::post('code') != 'DUM') {
					Session::set_flash('error', 'Cannot change "dummy" course\'s code.');
					Response::redirect('admin/courses'); return;
				}

				$course->special_id = Input::post('special_id');
				$course->code = Input::post('code');
				$course->code2 = Input::post('code2');
				$course->title = Input::post('title');
				$course->number_of_supervisors_winter = Input::post('number_of_supervisors_winter');
				$course->number_of_supervisors_summer = Input::post('number_of_supervisors_summer');
				$course->number_of_supervisors_september = Input::post('number_of_supervisors_september');

				if ($course->save())
				{
					// обновляем связи только если курс сохранился
					Model_Professorcourse::query()->where('course_id', $id)->delete();

					$new_professors = Input::post('professors');
					if (is_array($new_professors)) {
						foreach ($new_professors as $prof_id) {
							$props = array('professor_id' => $prof_id, 'course_id' => $id);
							(new Model_Professorcourse($props))->save();
						}
					}

					Session::set_flash('success', e('Updated course #' . $id));
					Response::redirect('admin/courses'); return;
				}
				else
				{
					Session::set_flash('error', e('Could not update course #' . $id));
				}
			}
			else
			{
				$course->special_id = $val->validated('special_id');
				$course->code = $val->validated('code');
				$course->code2 = $val->validated('code2');
				$course->title = $val->validated('title');
				$course->number_of_supervisors_winter = $val->validated('number_of_supervisors_winter');
				$course->number_of_supervisors_summer = $val->validated('number_of_supervisors_summer');
				$course->number_of_supervisors_september = $val->validated('number_of_supervisors_september');

				Session::set_flash('error', $val->error());
			}
		}

		$this->template->set_global('course', $course, false);
		$this->template->title = "";
		$this->template->content = View::forge('admin/courses/edit', $data);
	}

	public function action_delete($id = null)
	{
		if (!Auth::has_access('courses.delete')){
			Session::set_flash('error', e('You do not have access to this function!'));
			Response::redirect('admin/courses'); return;
		}

		// Ищем включая удалённые, чтобы корректно обработать повторное удаление
		$course = Model_Course::find_with_deleted($id);

		if ($course)
		{
			if(($course->title != 'DUMMY') && ($course->code != 'DUM')){
				if ($course->deleted_at !== null) {
					Session::set_flash('error', e('Course already deleted.'));
					Response::redirect('admin/courses'); return;
				}

				$course->soft_delete();
				Session::set_flash('success', e('Deleted course #'.$id));
			} else {
				Session::set_flash('error', e('Cannot delete DUMMY course! DUMMY course is important!!'));
			}
		}
		else
		{
			Session::set_flash('error', e('Could not delete course #'.$id));
		}

		Response::redirect('admin/courses'); return;
	}

	public function action_deleted()
	{
		if (!Auth::has_access('courses.read')){
			Session::set_flash('error', e('You do not have access to this function!'));
			Response::redirect('admin/courses'); return;
		}

		$data['courses'] = Model_Course::find_deleted();

		$this->template->title = "Deleted Courses";
		$this->template->content = View::forge('admin/courses/deleted', $data);
	}

	public function action_restore($id = null)
	{
		if (!Auth::has_access('courses.update')){
			Session::set_flash('error', e('You do not have access to this function!'));
			Response::redirect('admin/courses'); return;
		}

		$course = Model_Course::find_with_deleted($id);
		if (!$course) {
			Session::set_flash('error', e('Course not found.'));
			Response::redirect('admin/courses/deleted'); return;
		}

		if ($course->deleted_at === null) {
			Session::set_flash('error', e('Course is not deleted.'));
			Response::redirect('admin/courses/deleted'); return;
		}

		if ($course->title == 'DUMMY' || $course->code == 'DUM') {
			Session::set_flash('error', e('Cannot restore DUMMY course.'));
			Response::redirect('admin/courses/deleted'); return;
		}

		$course->restore();
		Session::set_flash('success', e('Restored course #'.$id));
		Response::redirect('admin/courses/deleted'); return;
	}

}