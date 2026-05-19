<?php

class Controller_Admin_Availabilitycalendar extends Controller_Admin
{
    public function action_index($id = null)
    {
        $examperiods = Model_Examperiod::find('all', [
            'order_by' => ['start' => 'desc'],
        ]);

        $years = [];
        foreach ($examperiods as $ep) {
            $years[$ep->academic_year] = $ep->academic_year;
        }

        $seasons = [
            'winter'    => 'Winter',
            'summer'    => 'Summer',
            'september' => 'September',
        ];

        $year   = Input::get('exam_year', Input::post('exam_year', null));
        $season = Input::get('season',   Input::post('season',   null));

        $examperiod = null;

        // 1) Если переданы GET/POST параметры (хотя бы один из них) — считаем, что юзер пытается открыть конкретный период
        if ($year !== null || $season !== null) {

            // передали только одно — сразу ошибка
            if (!$year || !$season) {
                Session::set_flash('error', 'Please select Exam Year and Exam Period.');
                return Response::redirect('admin/availability');
            }

            // проверяем, существует ли такой период
            $examperiod = Model_Examperiod::query()
                ->where('academic_year', $year)
                ->where('season', $season)
                ->get_one();

            if (!$examperiod) {
                Session::set_flash('error', 'The requested exam period does not exist.');
                return Response::redirect('admin/availability');
            }

        // 2) Если пришли по /index/{id}
        } elseif ($id !== null) {

            $examperiod = Model_Examperiod::find($id);

            if (!$examperiod) {
                Session::set_flash('error', 'The requested exam period does not exist.');
                return Response::redirect('admin/availability');
            }

        // 3) Если вообще ничего не передано — берём последний (как у тебя было)
        } else {

            $examperiod = Model_Examperiod::query()
                ->order_by('start', 'desc')
                ->get_one();

            if (!$examperiod) {
                Session::set_flash('error', 'No exam periods available.');
                return Response::redirect('admin/availability');
            }
        }

        $data = [];
        $data['selected_year']   = $examperiod->academic_year;
        $data['selected_season'] = $examperiod->season;
        $data['years']           = $years;
        $data['seasons']         = $seasons;

        $availabilities = [];

        foreach ($examperiod->examdays as $examday) {
            foreach ($examperiod->examhours as $examhour) {
                $key = "{$examday->id}|{$examhour->id}";
                $availabilities[$key] = [];
            }
        }

        foreach ($examperiod->doctoral_availabilities as $availability) {
            $doctoral_id   = $availability['doctoral_id'];
            $examcourse_id = $availability['examcourse_id'];

            $examcourse = null;
            foreach ($examperiod->examcourses as $ec) {
                if ((int)$ec->id === (int)$examcourse_id) {
                    $examcourse = $ec;
                    break;
                }
            }
            if (!$examcourse) continue;

            $key = "{$examcourse['examday_id']}|{$examcourse['examhour_id']}";
            if (!in_array($doctoral_id, $availabilities[$key], true)) {
                $availabilities[$key][] = $doctoral_id;
            }
        }

        $data['examperiod']      = $examperiod;
        $data['availabilities']  = $availabilities;

        $this->template->css   = ['availabilitycalendar.css'];
        $this->template->js    = ['availabilitycalendar.js'];
        $this->template->title = "Availabilities Calendar";
        $this->template->content = View::forge('admin/availabilitycalendar/index', $data);
    }
}
