<?php
class Controller_Admin_Welcome extends Controller_Admin{
		class Controller_Admin_Welcome extends Controller_Admin
	{
		public function before()
		{
			parent::before();
			// Announcements removed
			throw new HttpNotFoundException; 
		}

	}
	public function action_index()
	{
        if (!Auth::has_access('welcome.read')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }
        $config = array(
            'pagination_url' => Uri::base(false).'admin/welcome/index/',
            'total_items'    =>  Model_Welcome::count(),
            'per_page'       => 1,
            'uri_segment'    => 4,
            'num_links'     => 5,
            'show_first' => true,
            'show_last' => true,

        );
        $pagination = Pagination::forge('announcements_pagination', $config);


        $data['welcomes'] = Model_Welcome::query()
            ->rows_offset($pagination->offset)
            ->rows_limit( $pagination->per_page)
            ->order_by('id','desc')
            ->get();
        $data['pagination'] = $pagination;

        $this->template->css = array(
            'welcome-posts.css',
        );
		$this->template->title = "Announcements";
		$this->template->content = View::forge('admin/welcome/index', $data,false);

	}


	public function action_create()
	{
        if (!Auth::has_access('welcome.create')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/welcome');
        }
        /*
         * Purify Html (strip javascript actually).
         *
         */
        Package::load('fuel-htmlpurifier');
        $purifier_config = HTMLPurifier_Config::createDefault();
        //allow iframes from trusted sources
        $purifier_config->set('HTML.SafeIframe', true);
        $purifier_config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $purifier = new HTMLPurifier($purifier_config);

		if (Input::method() == 'POST')
		{
			$val = Model_Welcome::validate('create');
			
			if ($val->run())
			{
				$welcome = Model_Welcome::forge(array(
					'text' =>  $purifier->purify(Input::post('text')),

				));

				if ($welcome and $welcome->save())
				{
					Session::set_flash('success', 'Added note #'.$welcome->id.'.');

					Response::redirect('admin/welcome/index');
				}

				else
				{
					Session::set_flash('error', 'Could not save note.');
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
            'welcome-post-editor.js',
        );

        $this->template->title = "";
		$this->template->content = View::forge('admin/welcome/create');

	}

	public function action_edit($id = null)
	{
        if (!Auth::has_access('welcome.update')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/welcome');
        }
        /*
         * Purify Html (strip javascript actually).
         *
         */
        Package::load('fuel-htmlpurifier');
        $purifier_config = HTMLPurifier_Config::createDefault();
        //allow iframes from trusted sources
        $purifier_config->set('HTML.SafeIframe', true);
        $purifier_config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $purifier = new HTMLPurifier($purifier_config);

		is_null($id) and Response::redirect('admin/welcome/index');

		if ( ! $welcome = Model_Welcome::find($id))
		{
			Session::set_flash('error', 'Could not find note #'.$id);
			Response::redirect('admin/welcome/index');
		}

		$val = Model_Welcome::validate('edit');

		if ($val->run())
		{
			$welcome->text = $purifier->purify(Input::post('text'));


			if ($welcome->save())
			{
				Session::set_flash('success', 'Updated note #' . $id);

				Response::redirect('admin/welcome/index');
			}

			else
			{
				Session::set_flash('error', 'Could not update note #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$welcome->text = $val->validated('text');


				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('welcome', $welcome, false);
		}

        $this->template->js = array(
            '/vendor/ckeditor/ckeditor.js',
            '/vendor/ckeditor/adapters/jquery.js',
            'welcome-post-editor.js',
        );
        $this->template->title = "";
		$this->template->content = View::forge('admin/welcome/edit');

	}

	public function action_delete($id = null)
	{
        if (!Auth::has_access('welcome.delete')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/welcome');
        }

		is_null($id) and Response::redirect('admin/welcome');

		if ($welcome = Model_Welcome::find($id))
		{
			$welcome->delete();

			Session::set_flash('success', 'Deleted note #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete note #'.$id);
		}

		Response::redirect('admin/welcome');

	}


}