<?php

class Controller_Admin_Reports extends Controller_Admin
{

        public function before()
    {
        parent::before();

        if ( ! Auth::has_access('reports.read'))
        {
            Session::set_flash('error', 'Access denied.');
            Response::redirect('admin');
        }
    }

    /**
     * Report endpoint:
     *   /admin/reports/examperiod_noncompliance
     *   /admin/reports/examperiod_noncompliance/<examperiod_id>
     *
     * Also supports selecting the exam period via GET parameter:
     *   /admin/reports/examperiod_noncompliance?id=<examperiod_id>
     *
     * This action:
     * - loads available exam periods for a dropdown
     * - resolves the selected exam period
     * - fetches the report data via Model_Report
     * - renders the report view
     */
    public function action_examperiod_noncompliance($id = null)
    {
        $data = array();

        // Load all exam periods 
        $examperiods = Model_Examperiod::find('all');

        // Keep the old single dropdown data 
        $data['examperiods'] = array();
        foreach ($examperiods as $ep)
        {
            $data['examperiods'][$ep->id] = $ep->getGreekSeason($ep->season) . " " . $ep->academic_year;
        }

        // Build Year and Season dropdown structures
        $data['years'] = array();             // academic_year => label
        $data['seasons_by_year'] = array();   // academic_year => [season => label]

        foreach ($examperiods as $ep)
        {
            $y = $ep->academic_year;
            $s = $ep->season;

            if (!isset($data['years'][$y]))
            {
                $data['years'][$y] = $y;
            }

            if (!isset($data['seasons_by_year'][$y]))
            {
                $data['seasons_by_year'][$y] = array();
            }

            $data['seasons_by_year'][$y][$s] = $ep->getGreekSeason($s);
        }

        // Read selection from query string
        $get_id = Input::get('id', null);
        if ($get_id !== null && $get_id !== '')
        {
            $id = (int)$get_id;
        }
        else
        {
            $year   = Input::get('year', null);
            $season = Input::get('season', null);

            $data['selected_year'] = $year;
            $data['selected_season'] = $season;

            if ($year !== null && $year !== '' && $season !== null && $season !== '')
            {
                $resolved = Model_Examperiod::find('first', array(
                    'where' => array(
                        array('academic_year', $year),
                        array('season', $season),
                    ),
                ));

                if ($resolved)
                {
                    $id = (int)$resolved->id;
                }
            }
        }

        // Resolve exam period
        if ($id === null)
        {
            // Better default: pick the latest exam period by id
            $examperiod = Model_Examperiod::find('first', array(
                'order_by' => array('id' => 'desc'),
            ));
        }
        else
        {
            $examperiod = Model_Examperiod::find((int)$id);
        }

        // Validate that the exam period exists
        if ($examperiod === null)
        {
            Session::set_flash('error', 'The requested exam period does not exist');
            Response::redirect('admin/reports/examperiod_noncompliance');
            return;
        }

        // If year/season were not provided, default them based on the resolved examperiod
        if (!isset($data['selected_year']) || $data['selected_year'] === null || $data['selected_year'] === '')
        {
            $data['selected_year'] = $examperiod->academic_year;
        }
        if (!isset($data['selected_season']) || $data['selected_season'] === null || $data['selected_season'] === '')
        {
            $data['selected_season'] = $examperiod->season;
        }

        // Fetch the report data from the Report model
        $report = Model_Report::examperiod_noncompliance((int)$examperiod->id);

        // Pass data to the view
        $data['examperiod'] = $examperiod;
        $data['report']     = $report;

        // Render
        $this->template->title = "Exam Period Availability Report";
        $this->template->content = View::forge('admin/reports/examperiod_noncompliance', $data);
    }

}
