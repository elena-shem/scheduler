<?php

class Controller_Rest_Doctorals extends \Fuel\Core\Controller_Rest
{
    
    /**
     * http://<hostname>/rest/schedule/list.json?exid=53
     * 
     * Returns a JSON object containing an array of doctorals
     */
    public function get_list()
    {
        if(Input::get('exid', null) == null)
        {
            $exid = DB::select('id')
                ->from('examperiods')
                ->order_by('start','desc')
                ->limit(1)
                ->execute();
            $examperiod = Model_Examperiod::find($exid[0]["id"]);
        }
        else
        {
            $examperiod = Model_Examperiod::find(Input::get('exid', null));
        }
        
        if(!$examperiod)
        {
            return $this->response("Invalid examperiod");
        }
        
        
        $doctorals = array();
        if($examperiod->active == 1) {
            // If the examperiod is active then fetch the "active" doctorals.
            
            foreach(Model_Doctoral::find('all') as $id => $doctoral)
            {
                // If even one of the below filters is true, then do not include
                // the doctoral as candidate for assignments.
                if($doctoral["graduated"] != "0"
                || $doctoral["max_assignments"] == "0"
                || $doctoral["hours_remaining"] == "0")
                {
                    continue;
                }
                $doctorals[$id] = $doctoral;
            }
        } else {
            // If the examperiod is closed then fetch the assigned to this
            // examperiod doctorals.
            
            $doctoralsAll = Model_Doctoral::find('all');
            foreach($examperiod->examcourses as $examcourse) {
                foreach(explode(',', $examcourse->assignments) as $id) {
                    if(!isset($doctorals[$id]) && isset($doctoralsAll[$id])) {
                        $doctorals[$id] = $doctoralsAll[$id];
                    }
                }
            }
            
            uasort($doctorals, function($d1, $d2) {
                $result = strcmp($d1->surname, $d2->surname);
                return $result != 0 ? $result : strcmp($d1->name, $d2->name);
            });
        }
        
        $docs = array(null);
        foreach($doctorals as $id => $doctoral) {
            $professors = array();
            foreach($doctoral->professors as $professor) {
                $professors []= $professor->surname.' '.$professor->name;
            }
            
            $prefGen = Model_Preferencesgeneral::query()
                        ->where('doctoral_id', $id)
                        ->where('examperiod_id', $examperiod->id)
                        ->get_one();
            
            $prefAv  = Model_Preferencesavailable::query()
                        ->where('doctoral_id', $id)
                        ->where('examperiod_id', $examperiod->id)
                        ->get();
            
            $availabilities = array();
            foreach($prefAv as $av) {
                $availabilities []= $av->examcourse_id;
            }
            
            $docs []= array(
                "id"                => $id,
                "name"              => $doctoral["name"],
                "surname"           => $doctoral["surname"],
                "email"             => $doctoral["email"],
                "professors"        => $professors,
                "remainingHours"    => $doctoral["hours_remaining"],
                "comment"           => $prefGen["comment"],
                "density"           => $prefGen["density"],
                "densityDay"        => $prefGen["density_day"],
                "availabilities"    => $availabilities,
                "supsObliged"       => $doctoral["max_assignments"],
                "bonusWeight"       => $doctoral["bonus_weight"],
            );
        }
        
        return $docs;
    }
    
}

