<?php
class Controller_Admin_Users extends Controller_Admin{

	public function action_index()
	{
        if (!Auth::has_access('users.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        /**
         * Pagination
         */
        $config = array(
            'total_items'    => Model_User::count(),
            'per_page'       => 10,
            'uri_segment'    => 'page',
        );

        $pagination = Pagination::forge('mypagination', $config);

        /**
         * Data
         */
        $data['users'] = Model_User::query()
        ->rows_offset($pagination->offset)
        ->rows_limit($pagination->per_page)
        ->get();

        $data['pagination'] = $pagination;


		$this->template->title = "Users";
		$this->template->content = View::forge('admin/users/index', $data);

	}

	public function action_view($id = null)
	{
        if (!Auth::has_access('users.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/users');
        }

		$data['user'] = Model_User::find($id);

		$this->template->title = "";
		$this->template->content = View::forge('admin/users/view', $data);

	}

	public function action_create()
	{
        if (!Auth::has_access('users.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/users');
        }

		if (Input::method() == 'POST' && Security::check_token())
		{
			$val = Model_User::validate('create');

			if ($val->run())
			{
                /*$groups = Auth::groups();
                print_r($groups);
                exit(1);
                $groupNumber = array_search(Input::post('group'),$groups);*/

                try{
                    $user = Auth::create_user(Input::post('username'),Input::post('password'),Input::post('email'),Input::post('group'));
                }
                catch(exception $e){
                    Session::set_flash('error', e('Could not save user. '.$e->getMessage()));

                    Response::redirect('admin/users');

                }


				if ($user)
				{
					Session::set_flash('success', e('Added user #'.$user.'.'));

					Response::redirect('admin/users');
				}

				else
				{
					Session::set_flash('error', e('Could not save user.'));
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "";
		$this->template->content = View::forge('admin/users/create');

	}

	public function action_edit($id = null)
	{
        if (!Auth::has_access('users.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/users');
        }

		$user = Model_User::find($id);
		$val = Model_User::validate('edit');

		if ($val->run())
		{
            if (Input::method() == 'POST')
            {
                $user->group = Input::post('group');
                $user->email = Input::post('email');

                if ($user->save())
                {
                    Session::set_flash('success', e('Updated user #' . $id));

                    Response::redirect('admin/users');
                }

                else
                {
                    Session::set_flash('error', e('Could not update user #' . $id));
                }
            }
		}

		else
		{
			if (Input::method() == 'POST')
			{

				$user->group = $val->validated('group');
				$user->email = $val->validated('email');


				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('user', $user, false);
		}

		$this->template->title = "";
		$this->template->content = View::forge('admin/users/edit');

	}

	public function action_delete($id = null)
	{
        if (!Auth::has_access('users.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/users');
        }

		if ($user = Model_User::find($id))
		{
			$user->delete();

			Session::set_flash('success', e('Deleted user #'.$id));
		}

		else
		{
			Session::set_flash('error', e('Could not delete user #'.$id));
		}

		Response::redirect('admin/users');

	}

    public function action_edit_self()
    {

        list(, $userid) = Auth::get_user_id();

        $user = Model_User::find($userid);
        $val = Model_User::validate('edit_self');


        if ($val->run())
        {
            if (Input::method() == 'POST' && Security::check_token())
            {
                $user->group = Input::post('group');
                $user->email = Input::post('email');
                $password = Input::post('password');
                $old_password = Input::post('old_password');

                try{
                    if(!($pass_change = Auth::change_password($old_password,$password,$user->username))){
                        Session::set_flash('error', e('Could not change password!'));
                    }
                }catch(exception $e){
                    Session::set_flash('error', e('Could not change password!'.$e->getMessage()));

                }
                if ($pass_change and $user->save())
                {
                    Session::set_flash('success', e('Updated user #' . $userid));

                    Response::redirect('admin/users');
                }

                else
                {
                    Session::set_flash('error', e('Could not update user ' . $user->username . ($pass_change ? "" : ". Could not change password!")));
                }
            }
        }

        else
        {
            if (Input::method() == 'POST')
            {

                $user->group = $val->validated('group');
                $user->email = $val->validated('email');
                $password = $val->validated('password');
                $old_password = $val->validated('old_password');


                Session::set_flash('error', $val->error());
            }

            $this->template->set_global('user', $user, false);
        }

        $this->template->title = "";
        $this->template->content = View::forge('admin/users/edit_self');

    }


}
