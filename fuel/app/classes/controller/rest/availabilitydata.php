<?php

class Controller_Rest_Availabilitydata extends \Fuel\Core\Controller_Rest{


    public function post_list()
    {
        $given_exam_period_id = Input::post('exid',null);
        if(!is_numeric($given_exam_period_id)){
            return $this->response("Wrong exam period given!");
        }
        try{
            //Now this IS faster!
            //DB::expr used to prevent escaping the GROUP_CONCAT directive.
            //change the max group_concat length!
            //DB::query('SET SESSION group_concat_max_len = 200000')->execute();
            $result = DB::select('doc.id','doc.name','doc.surname','pa.examperiod_id',
                //DB::expr('GROUP_CONCAT(pa.examcourse_id) AS examcourse_ids'),
                //DB::expr('GROUP_CONCAT(ed.day) AS days'),
                //DB::expr('GROUP_CONCAT(CONCAT(eh.start,"-",eh.end)) AS hours'),
                //DB::expr('GROUP_CONCAT(CONCAT( pa.examcourse_id,"#",co.title,"#",ed.day,"#",eh.start,"#",eh.end)) AS allthemoney'),
                //DB::expr('GROUP_CONCAT(DISTINCT concat( ed.day, "#", eh.start, "-", eh.end) ORDER BY ed.day,eh.id ASC SEPARATOR \'\|\' ) AS distinct_hours'),
                DB::expr('GROUP_CONCAT(ed.id || "-" || eh.id) AS days_hours'),
                DB::expr('GROUP_CONCAT(profs.name || " " || profs.surname, ", ") AS professors'),
                'pg.density','pg.density_day','pg.comment')
                ->from(array('doctorals', 'doc'))
                ->join(array('preferencesavailable', 'pa'))->on('doc.id', '=', 'pa.doctoral_id')
                ->join(array('preferencesgeneral', 'pg'))->on('doc.id', '=', 'pg.doctoral_id')
                ->join(array('examcourses', 'ec'))->on('ec.id', '=', 'pa.examcourse_id')
                ->join(array('examdays', 'ed'))->on('ed.id', '=', 'ec.examday_id')
                ->join(array('examhours', 'eh'))->on('eh.id', '=', 'ec.examhour_id')
                ->join(array('courses', 'co'))->on('co.id', '=', 'ec.course_id')
                ->join(array(DB::expr('(
                         SELECT  doc2.id AS reldoc_id, prof.name,  prof.surname
                         FROM doctorals AS doc2
                         JOIN doctoralsupervisors AS rel ON rel.doctoral_id = doc2.id
                         JOIN professors AS prof ON prof.id = rel.professor_id
                         WHERE 1 )
                '), 'profs'),'LEFT OUTER')->on('profs.reldoc_id', '=', 'doc.id')
                ->where_open()->where('pa.examperiod_id', '=', $given_exam_period_id)->and_where('pg.examperiod_id', '=', $given_exam_period_id)->where_close()
                ->group_by([
                    'doc.id',
                    'doc.name',
                    'doc.surname',
                    'pa.examperiod_id',
                    'pg.density',
                    'pg.density_day',
                    'pg.comment',
                ])
                ->order_by('doc.surname','asc')->execute();

        }catch (Exception $e){
            \Log::error('availabilitydata SQL error: '.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response($result);
    }

//    public function get_list()
//    {//http://localhost/rest/availabilitydata/list.json?exid=50
//        $given_exam_period_id = Input::get('exid',null);
//        if(!is_numeric($given_exam_period_id)){
//            return $this->response("OOPS!");
//        }
//
//        try{
//            //Now this IS faster man!
//            //DB::expr used to prevent escaping the GROUP_CONCAT directive.
//            //change the max group_concat length!
//            DB::query('SET SESSION group_concat_max_len = 200000')->execute();
//            $result = DB::select('doc.id','doc.name','doc.surname','pa.examperiod_id',
//                //DB::expr('GROUP_CONCAT(pa.examcourse_id) AS examcourse_ids'),
//                //DB::expr('GROUP_CONCAT(ed.day) AS days'),
//                //DB::expr('GROUP_CONCAT(CONCAT(eh.start,"-",eh.end)) AS hours'),
//                //DB::expr('GROUP_CONCAT(CONCAT( pa.examcourse_id,"#",co.title,"#",ed.day,"#",eh.start,"#",eh.end)) AS allthemoney'),
//                //DB::expr('GROUP_CONCAT(DISTINCT concat( ed.day, "#", eh.start, "-", eh.end) ORDER BY ed.day,eh.id ASC SEPARATOR \'\|\' ) AS distinct_hours'),
//                DB::expr('GROUP_CONCAT(DISTINCT CONCAT(ed.id,"-",eh.id) ORDER BY ed.id,eh.id ASC ) AS days_hours'),
//                DB::expr('GROUP_CONCAT(DISTINCT CONCAT(profs.name," ",profs.surname) SEPARATOR \', \') AS professors'),
//                'pg.density','pg.density_day','pg.comment')
//                ->from(array('doctorals', 'doc'))
//                ->join(array('preferencesavailable', 'pa'))->on('doc.id', '=', 'pa.doctoral_id')
//                ->join(array('preferencesgeneral', 'pg'))->on('doc.id', '=', 'pg.doctoral_id')
//                ->join(array('examcourses', 'ec'))->on('ec.id', '=', 'pa.examcourse_id')
//                ->join(array('examdays', 'ed'))->on('ed.id', '=', 'ec.examday_id')
//                ->join(array('examhours', 'eh'))->on('eh.id', '=', 'ec.examhour_id')
//                ->join(array('courses', 'co'))->on('co.id', '=', 'ec.course_id')
//                ->join(array(DB::expr('(
//                         SELECT  doc2.id AS reldoc_id, prof.name,  prof.surname
//                         FROM doctorals AS doc2
//                         JOIN doctoralsupervisors AS rel ON rel.doctoral_id = doc2.id
//                         JOIN professors AS prof ON prof.id = rel.professor_id
//                         WHERE 1 )
//                '), 'profs'),'LEFT OUTER')->on('profs.reldoc_id', '=', 'doc.id')
//                ->where_open()->where('pa.examperiod_id', '=', $given_exam_period_id)->and_where('pg.examperiod_id', '=', $given_exam_period_id)->where_close()
//                ->group_by('doc.id')->execute();
//        }catch (Exception $e){
//            return $this->response($e->getMessage());
//        }
//
//
//
//
//
////SET SESSION group_concat_max_len = 200000;
////SELECT doc.id,doc.name,doc.surname,pa.examperiod_id,
////GROUP_CONCAT(DISTINCT CONCAT(ed.id,"-",eh.id) ORDER BY ed.id,eh.id ASC ) AS days_hours,pg.density,pg.density_day,pg.comment,
////GROUP_CONCAT(DISTINCT CONCAT(profs.name," ",profs.surname) SEPARATOR ', ') AS professors
////FROM doctorals AS doc
////JOIN preferencesavailable AS pa ON   doc.id = pa.doctoral_id
////JOIN preferencesgeneral AS pg ON doc.id = pg.doctoral_id
////JOIN examcourses AS ec ON ec.id = pa.examcourse_id
////JOIN examdays AS ed ON ed.id = ec.examday_id
////JOIN examhours AS eh ON eh.id = ec.examhour_id
////JOIN courses AS co ON co.id = ec.course_id
////LEFT OUTER JOIN (
////     SELECT  doc2.id AS reldoc_id, prof.name,  prof.surname
////     FROM doctorals AS doc2
////     JOIN doctoralsupervisors AS rel ON rel.doctoral_id = doc2.id
////     JOIN professors AS prof ON prof.id = rel.professor_id
////     WHERE 1
////) AS profs ON profs.reldoc_id = doc.id
////WHERE pa.examperiod_id = 50 AND pg.examperiod_id = 50
////GROUP BY doc.id
//
//
//
//        return $this->response($result);
//    }



}
