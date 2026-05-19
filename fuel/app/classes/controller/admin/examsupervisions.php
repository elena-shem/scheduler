<?php

class Controller_Admin_Examsupervisions extends Controller_Admin
{
    // List of all records
    public function action_index()
    {
        $data['supervisions'] = Model_ExamSupervision::find('all', array(
            'related' => array('doctoral', 'examcourse'),
            'order_by' => array('id' => 'desc'),
        ));

        $this->template->title   = "Exam Supervisions";
        $this->template->content = View::forge('admin/examsupervisions/index', $data);
    }

public function action_create()
{
    $posted_season_cache = Input::post('examperiod_id', null);

    if (Input::method() == 'POST')
    {
        $doctoral_ids  = (array) Input::post('doctoral_id', []);
        $attended_list = (array) Input::post('attended', []);
        $comments_list = (array) Input::post('comment', []);
        
        $examcourse_id    = Input::post('examcourse_id');
        $examcourse_title = Input::post('examcourse_title'); 
        $posted_season    = Input::post('examperiod_id');    
        $posted_year      = Input::post('exam_year'); 
        
        $posted_day  = Input::post('exam_day', '');
        $posted_hour = Input::post('exam_hour', '');
        $posted_hour = html_entity_decode($posted_hour, ENT_QUOTES, 'UTF-8');

        $real_period_id = null;
        if ($posted_season && $posted_year) {
            $period = Model_Examperiod::query()
                ->where('season', trim($posted_season))
                ->where('academic_year', trim($posted_year))
                ->get_one();
            if ($period) {
                $real_period_id = $period->id;
            }
        }

        if ( ! $examcourse_id && ! $examcourse_title) {
            Session::set_flash('error', 'Please select an exam course.');
        } else {
            $base_course_id = null;
            $original_day = '';
            $original_hour = '';

            if ($examcourse_id) {
                $course_original = Model_Examcourse::find($examcourse_id, array('related' => array('course', 'examday', 'examhour')));
                if ($course_original) {
                    $base_course_id = $course_original->course_id;
                    $original_day = $course_original->examday ? $course_original->examday->day : '';
                    $original_hour = $course_original->examhour ? ($course_original->examhour->start . '–' . $course_original->examhour->end) : '';
                    $original_hour = html_entity_decode($original_hour, ENT_QUOTES, 'UTF-8');
                }
            } 

            if (!$base_course_id && $examcourse_title) {
                $c = Model_Course::query()->where('title', trim($examcourse_title))->get_one();
                if ($c) {
                    $base_course_id = $c->id;
                }
            }

            if (!$base_course_id) {
                Session::set_flash('error', 'Selected course not found in database.');
            } else {
                if ($real_period_id) {
                    $actual_course = Model_Examcourse::query()
                        ->where('course_id', $base_course_id)
                        ->where('examperiod_id', $real_period_id)
                        ->get_one();

                    if (!$actual_course) {
                        $actual_course = Model_Examcourse::forge([
                            'course_id'     => $base_course_id,
                            'examperiod_id' => $real_period_id, 
                            'examday_id'    => null, 
                            'examhour_id'   => null,
                        ]);
                        $actual_course->save();
                    }

                    if (!isset($course_original) || $actual_course->examperiod_id != $course_original->examperiod_id) {
                        $original_day = '';
                        $original_hour = '';
                    }
                    
                    $examcourse_id = $actual_course->id;
                }

                $custom_day  = (trim($posted_day) !== trim($original_day)) ? trim($posted_day) : null;
                $custom_hour = (trim($posted_hour) !== trim($original_hour)) ? trim($posted_hour) : null;

                $saved = 0;

                foreach ($doctoral_ids as $i => $doc_id) {
                    $doc_id = trim((string)$doc_id);
                    if ($doc_id === '') continue;

                    $attended = isset($attended_list[$i]) ? (int)$attended_list[$i] : 1;

                    $exists = Model_ExamSupervision::query()
                        ->where('doctoral_id', $doc_id)
                        ->where('examcourse_id', $examcourse_id)
                        ->get_one();
                    if ($exists) continue;

                    $comment = isset($comments_list[$i]) ? trim($comments_list[$i]) : null;

                    $supervision = Model_ExamSupervision::forge([
                        'doctoral_id'      => $doc_id,
                        'examcourse_id'    => $examcourse_id,
                        'attended'         => $attended,
                        'comment'          => $comment ?: null,
                        'custom_exam_day'  => $custom_day,
                        'custom_exam_hour' => $custom_hour,
                    ]);

                    $supervision->hours = $supervision->calculate_hours();

                    if ($supervision->save()) $saved++;
                }

                if ($saved > 0) {
                    Session::set_flash('success', "Saved {$saved} supervision(s) successfully.");
                    Response::redirect('admin/examsupervisions');
                } else {
                    Session::set_flash('error', 'Nothing was saved (duplicates or no doctor selected?).');
                }
            }
        }
    }

    $cache_get = function ($key) {
        try {
            return \Cache::get($key);
        } catch (\CacheNotFoundException $e) {
            return null;
        } catch (\CacheExpiredException $e) {
            return null;
        } catch (\CacheException $e) {
            return null;
        }
    };

    // Data for the form (CACHED)
    $data['doctorals'] = $cache_get('es_doctorals_active');
    if (empty($data['doctorals'])) {
        $data['doctorals'] = Model_Doctoral::find('all', array(
            'where' => array(
                array('deleted_at', '=', null),
                array('graduated', '=', 0),
            ),
            'order_by' => array('surname' => 'asc'),
        ));
        \Cache::set('es_doctorals_active', $data['doctorals'], 300);
    }

    $data['examperiods'] = $cache_get('es_examperiods_all');
    if (empty($data['examperiods'])) {
        $data['examperiods'] = Model_Examperiod::find('all');
        \Cache::set('es_examperiods_all', $data['examperiods'], 300);
    }

    $cacheKey = $posted_season_cache ? 'es_examcourses_options_p'.$posted_season_cache : 'es_examcourses_options_all';
    $data['examcourses_options'] = $cache_get($cacheKey);

    if (empty($data['examcourses_options'])) {

        $examcourses = Model_Examcourse::query()
            ->related(['course','examday','examhour','examperiod']);

        if ($posted_season_cache) {
            $examcourses->where('examperiod_id', $posted_season_cache);
        }

        $examcourses = $examcourses->get();

        $examcourses_options = [];
        foreach ($examcourses as $e) {
            $course = $e->course;
            if (!$course) continue;

            $title = $course->title;

            if (!isset($examcourses_options[$title])) {
                $examcourses_options[$title] = [];
            }

            $hour = $e->examhour ? ($e->examhour->start . '–' . $e->examhour->end) : '';
            $hour = html_entity_decode($hour, ENT_QUOTES, 'UTF-8');

            $examcourses_options[$title][] = [
                'id'     => $e->id,
                'day'    => $e->examday ? $e->examday->day : '',
                'hour'   => $hour,
                'year'   => $e->examperiod->academic_year,
                'season' => $e->examperiod->season,
            ];
        }

        ksort($examcourses_options, SORT_STRING | SORT_FLAG_CASE);

        $data['examcourses_options'] = $examcourses_options;
        \Cache::set($cacheKey, $data['examcourses_options'], 300);
    }

    $data['selected_examperiod_id'] = $posted_season_cache;
    $data['type'] = 'create';
    $data['examcourses'] = []; 

    $this->template->title   = "Add Supervision";
    $this->template->content = View::forge('admin/examsupervisions/create', $data);
}

    // Editing an existing entry
    public function action_edit($id = null)
    {
        is_null($id) and Response::redirect('admin/examsupervisions');
        $supervision = Model_ExamSupervision::find($id, [
            'related' => [
                'doctoral',
                'examcourse' => ['course', 'examday', 'examhour', 'examperiod'],
            ],
        ]);

        if ( ! $supervision)
        {
            Session::set_flash('error', 'Entry not found.');
            Response::redirect('admin/examsupervisions');
        }
if (Input::method() == 'POST')
{
    $doctoral_ids = Input::post('doctoral_id', []);
    $attendeds    = Input::post('attended', []);
    $comments     = Input::post('comment', []);


    if (!is_array($doctoral_ids)) $doctoral_ids = [$doctoral_ids];
    if (!is_array($attendeds))    $attendeds    = [$attendeds];
    if (!is_array($comments))     $comments     = [$comments];

    $supervision->doctoral_id = isset($doctoral_ids[0]) ? (int)$doctoral_ids[0] : $supervision->doctoral_id;
    $supervision->attended    = isset($attendeds[0])    ? (int)$attendeds[0]    : 1;

    $comment0 = isset($comments[0]) ? trim((string)$comments[0]) : '';
    $supervision->comment = ($comment0 !== '') ? $comment0 : null;

    $supervision->hours = $supervision->calculate_hours();

    if ($supervision->save())
    {
        Session::set_flash('success', 'Supervision updated successfully.');
        Response::redirect('admin/examsupervisions');
    }
    else
    {
        Session::set_flash('error', 'Failed to update entry.');
    }
}
        $data['supervision'] = $supervision;
        $data['doctorals'] = Model_Doctoral::find('all', array(
            'where' => array(
                array('deleted_at', '=', null),
                array('graduated', '=', 0),
            ),
            'order_by' => array('surname' => 'asc'),
        ));

        $current_doc = Model_Doctoral::find($supervision->doctoral_id);
        if ($current_doc) {
            $found = false;
            foreach ($data['doctorals'] as $d) {
                if ((int)$d->id === (int)$current_doc->id) { $found = true; break; }
            }
            if (!$found) {
                $data['doctorals'][] = $current_doc;
            }
        }

        $data['examperiods'] = Model_Examperiod::find('all');
        $data['examcourses'] = []; 

        $data['selected_examperiod_id'] = null;
        $data['type'] = 'edit';

        $this->template->title   = "Edit Supervision";
        $this->template->content = View::forge('admin/examsupervisions/create', $data);
    }

    // Delete a record
    public function action_delete($id = null)
        {
            $supervision = Model_ExamSupervision::find($id);

            if ($supervision) {
                $supervision->delete();
                Session::set_flash('success', 'Entry deleted.');
            } else {
                Session::set_flash('error', 'Entry not found.');
            }

            Response::redirect('admin/examsupervisions');
        }
}