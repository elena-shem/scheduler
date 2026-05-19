<?php


class Controller_About extends Controller_Template
{
    
	public function action_index()
	{
        $data = array();
        if(Auth::check()) {
            $data['current_user'] = Auth::get('username');
            $this->template->current_user = Auth::get('username');
        }
        
		$this->template->title = "About";
        $this->template->css = array('about.css');
		$this->template->content = View::forge('about/index', $data, false);
	}
    
}

