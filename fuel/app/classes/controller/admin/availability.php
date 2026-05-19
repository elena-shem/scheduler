<?php
class Controller_Admin_Availability extends Controller_Admin
{
    public function action_index()
    {
        if (!Auth::has_access('availability.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $season_en = array(
            'winter'    => 'Winter',
            'summer'    => 'Summer',
            'september' => 'September',
        );

        $exam_periods = Model_Examperiod::find('all');

        $data['exam_periods'] = array('' => '');
        $data['examperiod_options'] = array();
        $data['examperiod_map'] = array();

        foreach ($exam_periods as $p) {

            if (isset($season_en[$p->season])) {
                $label = $season_en[$p->season];
            } else {
                $label = ucfirst($p->season);
            }

            // если где-то это используется — оставляем
            $data['exam_periods'][$p->id] = $label . ' ' . $p->academic_year;

            // options (season -> label) для select
            if (!isset($data['examperiod_options'][$p->season])) {
                $data['examperiod_options'][$p->season] = array(
                    'label' => $label
                );
            }

            // map (season -> year -> id)
            if (!isset($data['examperiod_map'][$p->season])) {
                $data['examperiod_map'][$p->season] = array();
            }
            $data['examperiod_map'][$p->season][$p->academic_year] = (int)$p->id;
        }

        $this->template->js = array(
            'vendor/jsviews.js',
            'vendor/sortable.js',
            'availability.js',
        );
        $this->template->css = array(
            'availability.css',
        );

        $this->template->title = "Supervisors' Availability";
        $this->template->content = View::forge('admin/availability/index', $data);
    }
}
