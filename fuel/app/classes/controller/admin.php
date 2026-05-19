<?php

class Controller_Admin extends Controller_Base
{
	public $template = 'admin/template';

	public function before()
	{
		parent::before();

		if (Request::active()->controller !== 'Controller_Admin' or ! in_array(Request::active()->action, array('login', 'logout')))
		{
			if (Auth::check())
			{
				$admin_group_id = Config::get('auth.driver', 'Simpleauth') == 'Ormauth' ? 6 : 100;
                $moderator_group_id = Config::get('auth.driver', 'Simpleauth') == 'Ormauth' ? 5 : 50;
				if ( ! Auth::member($admin_group_id) && !Auth::member($moderator_group_id))
				{
					Session::set_flash('error', e('You don\'t have access to the admin panel'));
					return Response::redirect('/');
				}
			}
			else
			{
				Response::redirect('admin/login');
			}
		}
	}

	public function action_login()
	{
		// Already logged in
		Auth::check() and Response::redirect('admin');

		$val = Validation::forge();

		if (Input::method() == 'POST')
		{
			$val->add('email', 'Email or Username')
			    ->add_rule('required');
			$val->add('password', 'Password')
			    ->add_rule('required');

			if ($val->run())
			{
				$auth = Auth::instance();

				// check the credentials. This assumes that you have the previous table created
				if (Auth::check() or $auth->login(Input::post('email'), Input::post('password')))
				{
					// credentials ok, go right in
					if (Config::get('auth.driver', 'Simpleauth') == 'Ormauth')
					{
						$current_user = Model\Auth_User::find_by_username(Auth::get_screen_name());
					}
					else
					{
						$current_user = Model_User::find_by_username(Auth::get_screen_name());
					}
					Response::redirect('admin');
				}
				else
				{
					$this->template->set_global('login_error', 'Wrong username and/or password!');
				}
			}
		}

		$this->template->title = 'Login';
		$this->template->content = View::forge('admin/login', array('val' => $val), false);
	}

	/**
	 * The logout action.
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_logout()
	{
		Auth::logout();
		Response::redirect('admin');
	}

	/**
	 * The index action.
	 *
	 * @access  public
	 * @return  void
	 */

	public function action_index()
	{
		return Response::redirect('admin/courses');
	}

    public function after($response){
        $this->template->navigation = \Fuel\Core\View::forge('admin/navigation');
        return parent::after($response);
    }

    //make clicked links active
    public static function echoActiveClassIfRequestMatches($requestUri,$doneItOnce = true)
    {
        $serverUri = $_SERVER['REQUEST_URI'];
        $fileName = basename($serverUri, ".php");
        $folderName = dirname($serverUri);
        $folderName = str_replace('/','',$folderName);
        $currentFileName = $folderName.'/'.$fileName;

        if ($currentFileName == $requestUri){
            echo 'class="active"';
        }
        else if( ($fileName == "") && !$doneItOnce){
            echo 'class="active"';
        }
    }

}

/* End of file admin.php */
