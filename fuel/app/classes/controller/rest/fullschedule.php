<?php

class Controller_Rest_Fullschedule extends \Fuel\Core\Controller_Rest{



    public function post_list()
    {//http://localhost/rest/fullschedule/list.json?exid=50
        $given_exam_period_id = Input::post('exid',null);
        if(!is_numeric($given_exam_period_id)){
            return $this->response("OOPS!");
        }
        try{
            $days = DB::select(array('ed.id','day_id'), DB::expr("DATE_FORMAT(ed.day, '%W %e/%c/%y') AS day"))
                ->from(array('examdays', 'ed'))
                ->where_open()->where('ed.examperiod_id', '=', $given_exam_period_id)->where_close()
                ->order_by('ed.day','asc')
                ->execute();
            $hours = DB::select(array('eh.id','hour_id'),DB::expr("TIME_FORMAT(eh.start, '%H:%i') AS hour_start, TIME_FORMAT(eh.end, '%H:%i') AS hour_end"))
                ->from(array('examhours', 'eh'))
                ->where_open()->where('eh.examperiod_id', '=', $given_exam_period_id)->where_close()
                ->order_by('eh.start','asc')
                ->execute();
        }catch (Exception $e){
            return $this->response($e->getMessage());
        }

        return $this->response(array('days'=>$days->as_array(),'hours'=>$hours->as_array()));
    }

    public function get_list()
    {//http://localhost/rest/fullschedule/list.json?exid=50
        $given_exam_period_id = Input::get('exid',null);
        if(!is_numeric($given_exam_period_id)){
            return $this->response("OOPS!");
        }

        try{
            $days = DB::select(array('ed.id','day_id'), DB::expr("DATE_FORMAT(ed.day, '%W %e/%c/%y') AS day"))
                ->from(array('examdays', 'ed'))
                ->where_open()->where('ed.examperiod_id', '=', $given_exam_period_id)->where_close()
                ->order_by('ed.day','asc')
                ->execute();
            $hours = DB::select(array('eh.id','hour_id'),DB::expr("TIME_FORMAT(eh.start, '%H:%i') AS hour_start, TIME_FORMAT(eh.end, '%H:%i') AS hour_end"))
                ->from(array('examhours', 'eh'))
                ->where_open()->where('eh.examperiod_id', '=', $given_exam_period_id)->where_close()
                ->order_by('eh.start','asc')
                ->execute();





        }catch (Exception $e){
            return $this->response($e->getMessage());
        }

        return $this->response(array('days'=>$days->as_array(),'hours'=>$hours->as_array()));
    }
}