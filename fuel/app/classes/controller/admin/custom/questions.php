<?php
class Controller_Admin_Custom_Questions extends Controller_Admin{

    public function before()
    {
        parent::before();
        // Questions page removed
        Session::set_flash('error', e('This page is no longer available.'));
        Response::redirect('admin');
    }

    public function action_index()
    {
        if (!Auth::has_access('emails.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $data['questions'] = Model_Custom_Question::find('all');

        $this->template->title = "Questions";
        $this->template->content = View::forge('admin/custom/questions/index', $data);

    }

    public function action_view($id = null)
    {
        if (!Auth::has_access('emails.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/custom/questions');
        }
        $data['question'] = Model_Custom_Question::find($id);


        $this->template->css = array(
            'table-view.css'
        );
        $this->template->title = "Question";
        $this->template->content = View::forge('admin/custom/questions/view', $data);

    }

    public function action_create()
    {
        if (!Auth::has_access('emails.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/custom/questions');
        }

        if (Input::method() == 'POST' && Security::check_token())
        {
            $val = Model_Custom_Question::validate('create');

            if ($val->run())
            {
                $question = Model_Custom_Question::forge(array(
                    'title' => Input::post('title'),
                    'question_html' => Input::post('question_html')
                ));

                if ($question and $question->save())
                {
                    Session::set_flash('success', e('Added question #'.$question->id.'.'));

                    Response::redirect('admin/custom/questions');
                }

                else
                {
                    Session::set_flash('error', e('Could not save question.'));
                }
            }
            else
            {
                Session::set_flash('error', $val->error());
            }
        }

        $this->template->js = array(
            '/vendor/ckeditor/ckeditor.js',
            '/vendor/ckeditor/adapters/jquery.js',
            'questions/question-editor.js',
        );
        $this->template->title = "Questions";
        $this->template->content = View::forge('admin/custom/questions/create');

    }

    public function action_edit($id = null)
    {
        if (!Auth::has_access('emails.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/custom/questions');
        }



        $question = Model_Custom_Question::find($id);
        $val = Model_Custom_Question::validate('edit');


        if ($val->run())
        {
            if (Input::method() == 'POST' && Security::check_token())
            {
                $question->title = Input::post('title');
                $question->question_html = Input::post('question_html');

                if ($question->save())
                {
                    Session::set_flash('success', e('Updated question #' . $id));

                    Response::redirect('admin/custom/questions');
                }

                else
                {
                    Session::set_flash('error', e('Could not update question #' . $id));
                }
            }
        }

        else
        {
            if (Input::method() == 'POST')
            {
                $question->title = $val->validated('title');
                $question->question_html = $val->validated('question_html');

                Session::set_flash('error', $val->error());
            }

            $this->template->set_global('question', $question, false);
        }

        $this->template->js = array(
            '/vendor/ckeditor/ckeditor.js',
            '/vendor/ckeditor/adapters/jquery.js',
            'questions/question-editor.js',
        );
        $this->template->title = "Questions";
        $this->template->content = View::forge('admin/custom/questions/edit');

    }

    public function action_delete($id = null)
    {
        if (!Auth::has_access('emails.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/custom/questions');
        }

        if ($professor = Model_Custom_Question::find($id))
        {
            $professor->delete();

            Session::set_flash('success', e('Deleted question #'.$id));
        }

        else
        {
            Session::set_flash('error', e('Could not delete question #'.$id));
        }

        Response::redirect('admin/custom/questions');

    }


}