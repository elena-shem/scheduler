<?php

class Controller_Rest_Time extends \Fuel\Core\Controller_Rest{

    /**
     * @return object
     * Returns time in milliseconds
     */

    public function post_list()
    {
        //set timezone
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

        return $this->response(microtime(true));
    }

    public function get_list()
    {
        //set timezone
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));

        return $this->response(microtime(true));
    }
}