<?php
class Controller_Welcome extends Controller_Template{
    public function action_index()
    {
        if (!Auth::check()) {
            Response::redirect('admin/login');
        }

        Response::redirect('admin');
    }
}
