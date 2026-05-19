<?php

class Controller_Admin_Assigner_Emails extends Controller_Admin

{
    /**
     * Доступ: кто может рассылать письма преподавателям.
     */
    public function before()
    {
        parent::before();

        if ( ! \Auth::has_access('emails.read') && ! \Auth::has_access('assigner.read'))
        {
            \Session::set_flash('error', 'You do not have access to this function!');
            \Response::redirect('admin');
        }

        // Убедимся, что пакет email загружен 
        if ( ! \Package::loaded('email'))
        {
            \Package::load('email');
        }
    }

public function action_preview($exid = null)
{
    $exid = (int)$exid;
    if ($exid <= 0) {
        \Session::set_flash('error', 'Missing examperiod id');
        return \Response::redirect('admin/assigner_emails');
    }

    $payload = $this->build_payload($exid);
    $professor_id = (int)\Input::get('professor_id', 0);

    if ($professor_id > 0 && isset($payload['by_professor'][$professor_id]))
    {
        $profData = $payload['by_professor'][$professor_id];

        $viewData = $profData;
        $viewData['examperiod'] = $payload['examperiod'];

        // тело письма
        $email_html = \View::forge('admin/emails/assigner_emails_body', $viewData)->render();

        // === DEBUG START ===
        \Log::warning('EMAIL_HTML_DEBUG_FIRST_300: ' . substr($email_html, 0, 300));
        \Log::warning('EMAIL_HTML_DEBUG_HAS_LT: ' . (strpos($email_html, '&lt;') !== false ? 'YES' : 'NO'));
        // === DEBUG END ===

        // страница предпросмотра
        $data = array(
            'examperiod' => $payload['examperiod'],
            'professor'  => $profData['professor'],
            'email_html' => $email_html,
        );

        $view = \View::forge('admin/emails/assigner_emails_preview', $data);
        $view->set_safe('email_html', $email_html);

        $this->template->title = 'Email preview';
        $this->template->content = $view;
        return;
    }

    $data = array(
        'examperiod'   => $payload['examperiod'],
        'by_professor' => $payload['by_professor'],
    );

    $this->template->title = 'Preview recipients';
    $this->template->content = \View::forge('admin/emails/assigner_emails_recipients', $data);
    return;
}

    public function action_send($exid = null)
    {
        if (\Input::method() !== 'POST')
        {
            \Session::set_flash('error', 'Invalid request method.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        if ( ! \Security::check_token())
        {
            \Session::set_flash('error', 'Invalid CSRF token.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        $exid = (int) $exid;
        if ($exid <= 0)
        {
            \Session::set_flash('error', 'Missing examperiod id');
            return \Response::redirect('admin');
        }

        $payload = $this->build_payload($exid);

        if (empty($payload['by_professor']))
        {
            \Session::set_flash('error', 'No recipients found (no assigned examcourses).');
            return \Response::redirect('admin');
        }

        $sent = 0;
        $failed = 0;

        foreach ($payload['by_professor'] as $professor_id => $profData)
        {
            $to = $profData['professor']['email'];

            if (empty($to))
            {
                $failed++;
                \Log::warning("Assigner Emails: professor {$professor_id} has empty email.");
                continue;
            }

            $viewData = $profData;
            $viewData['examperiod'] = $payload['examperiod'];

            $body_html = \View::forge('admin/emails/assigner_emails_body', $viewData)->render();

            $subject = 'Exam invigilator assignments — '
                . $payload['examperiod']['label'];

            try {
                \Log::warning('LECTURER EMAIL DEBUG: ' . print_r(array(
                    'to' => $to,
                    'subject' => $subject,
                    'professor_id' => $professor_id,
                    'examperiod_id' => $exid,
                    'courses_count' => count($profData['examcourses']),
                ), true));

                $email = \Email::forge();
                $email->to($to);
                $email->subject($subject);
                $email->html_body($body_html);

                $bcc_email = \Config::get('email.admin_bcc');
                if (!empty($bcc_email)) {
                    $email->bcc($bcc_email);
                }

                $email->send();

                $sent++;
            }
            catch (\Exception $e)
            {
                $failed++;
                \Log::error("Assigner Emails: send failed to {$to}. Error: ".$e->getMessage());
            }
        }

        \Session::set_flash('success', "Processed. Sent: {$sent}, failed: {$failed}.");
        return \Response::redirect('admin/assigner_emails/preview/'.$exid);
    }

    public function action_send_one($exid = null, $professor_id = null)
    {
        if (\Input::method() !== 'POST')
        {
            \Session::set_flash('error', 'Invalid request method.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        if ( ! \Security::check_token())
        {
            \Session::set_flash('error', 'Invalid CSRF token.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        $exid = (int)$exid;
        $professor_id = (int)$professor_id;

        if ($exid <= 0 || $professor_id <= 0)
        {
            \Session::set_flash('error', 'Missing parameters.');
            return \Response::redirect('admin/assigner_emails');
        }

        $payload = $this->build_payload($exid);

        if (!isset($payload['by_professor'][$professor_id]))
        {
            \Session::set_flash('error', 'Professor not found in recipients list for this exam period.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        $profData = $payload['by_professor'][$professor_id];
        $to = $profData['professor']['email'];

        if (empty($to))
        {
            \Session::set_flash('error', 'Professor has empty email.');
            return \Response::redirect('admin/assigner_emails/preview/'.$exid);
        }

        $viewData = $profData;
        $viewData['examperiod'] = $payload['examperiod'];

        $body_html = \View::forge('admin/emails/assigner_emails_body', $viewData)->render();

        $subject = 'Exam invigilator assignments — ' . $payload['examperiod']['label'];

        try
        {
            \Log::warning('LECTURER EMAIL DEBUG (ONE): ' . print_r(array(
                'to' => $to,
                'subject' => $subject,
                'professor_id' => $professor_id,
                'examperiod_id' => $exid,
                'courses_count' => count($profData['examcourses']),
            ), true));

            $email = \Email::forge();
            $email->to($to);
            $email->subject($subject);
            $email->html_body($body_html);

            $bcc_email = \Config::get('email.admin_bcc');
            if (!empty($bcc_email)) {
                $email->bcc($bcc_email);
            }

            $email->send();

            \Session::set_flash('success', 'Email sent to '.$to);
        }
        catch (\Exception $e)
        {
            \Log::error("Assigner Emails: send_one failed to {$to}. Error: ".$e->getMessage());
            \Session::set_flash('error', 'Send failed: '.$e->getMessage());
        }

        return \Response::redirect('admin/assigner_emails/preview/'.$exid.'?professor_id='.$professor_id);
    }


    private function build_payload($exid)
    {
        // examperiod
        $examperiod = \Model_Examperiod::find($exid);
        if ( ! $examperiod)
        {
            throw new \HttpNotFoundException('Examperiod not found');
        }

        $examperiod_label = $examperiod->getGreekSeason($examperiod->season) . ' ' . $examperiod->academic_year;

        // Забираем все examcourses этого периода с непустыми assignments
        $sql = "
            SELECT
                ec.id AS examcourse_id,
                ec.course_id,
                ec.examday_id,
                ec.examhour_id,
                ec.assignments,
                c.code AS course_code,
                c.title AS course_title,
                ed.day AS exam_day,
                eh.start AS exam_start,
                eh.end AS exam_end
            FROM examcourses ec
            JOIN courses c ON c.id = ec.course_id
            JOIN examdays ed ON ed.id = ec.examday_id
            JOIN examhours eh ON eh.id = ec.examhour_id
            WHERE ec.examperiod_id = :exid
              AND ec.assignments IS NOT NULL
              AND ec.assignments <> ''
            ORDER BY ed.day, eh.start
        ";

        $rows = \DB::query($sql)->parameters(array('exid' => $exid))->execute()->as_array();

        if (empty($rows))
        {
            return array(
                'examperiod' => array('id' => $exid, 'label' => $examperiod_label),
                'by_professor' => array(),
            );
        }

        // Собираем doctorals ids
        $doctoral_ids = array();
        foreach ($rows as $r)
        {
            $ids = array_filter(array_map('trim', explode(',', $r['assignments'])));
            foreach ($ids as $did)
            {
                $did = (int) $did;
                if ($did > 0) $doctoral_ids[$did] = $did;
            }
        }

        // Загружаем докторантов пачкой
        $doctorals_map = array();
        if ( ! empty($doctoral_ids))
        {
            $doc_rows = \DB::select('id', 'surname', 'name', 'email')
                ->from('doctorals')
                ->where('id', 'in', array_values($doctoral_ids))
                ->execute()
                ->as_array();

            foreach ($doc_rows as $d)
            {
                $doctorals_map[(int)$d['id']] = $d;
            }
        }

        // Мап course_id -> professors (εισηγητές) через professorcourses
        $course_ids = array();
        foreach ($rows as $r) { $course_ids[(int)$r['course_id']] = (int)$r['course_id']; }

        $pc_rows = \DB::query("
            SELECT
                pc.course_id,
                p.id AS professor_id,
                p.surname,
                p.name,
                p.email
            FROM professorcourses pc
            JOIN professors p ON p.id = pc.professor_id
            WHERE pc.course_id IN (" . implode(',', array_map('intval', array_values($course_ids))) . ")
        ")->execute()->as_array();

        $course_professors = array(); // course_id => [professor_id => professor]
        foreach ($pc_rows as $pr)
        {
            $cid = (int)$pr['course_id'];
            $pid = (int)$pr['professor_id'];
            if ( ! isset($course_professors[$cid])) $course_professors[$cid] = array();
            $course_professors[$cid][$pid] = array(
                'id' => $pid,
                'surname' => $pr['surname'],
                'name' => $pr['name'],
                'email' => $pr['email'],
            );
        }

        // Строим by_professor
        $by_professor = array();

        foreach ($rows as $r)
        {
            $cid = (int) $r['course_id'];
            if (empty($course_professors[$cid]))
            {
                // Нет εισηγητή для курса — пропускаем или логируем
                \Log::warning("Assigner Emails: no professors found for course_id={$cid}");
                continue;
            }

            // assigned doctorals для этого examcourse
            $assigned = array();
            $ids = array_filter(array_map('trim', explode(',', $r['assignments'])));
            foreach ($ids as $did)
            {
                $did = (int)$did;
                if ($did > 0 && isset($doctorals_map[$did]))
                {
                    $assigned[] = $doctorals_map[$did];
                }
            }

            // каждому преподавателю этого курса добавляем этот examcourse
            foreach ($course_professors[$cid] as $pid => $prof)
            {
                if ( ! isset($by_professor[$pid]))
                {
                    $by_professor[$pid] = array(
                        'professor' => $prof,
                        'examcourses' => array(),
                    );
                }

                $by_professor[$pid]['examcourses'][] = array(
                    'examcourse_id' => (int)$r['examcourse_id'],
                    'course_id'     => $cid,
                    'course_code'   => $r['course_code'],
                    'course_title'  => $r['course_title'],
                    'exam_day'      => $r['exam_day'],
                    'exam_start'    => $r['exam_start'],
                    'exam_end'      => $r['exam_end'],
                    'assigned_doctorals' => $assigned,
                );
            }
        }
       // --- sort by professor surname/name ---
if (class_exists('Collator')) {
    $coll = new \Collator('el_GR');

    uasort($by_professor, function($a, $b) use ($coll) {
        $as = isset($a['professor']['surname']) ? $a['professor']['surname'] : '';
        $bs = isset($b['professor']['surname']) ? $b['professor']['surname'] : '';

        $cmp = $coll->compare($as, $bs);
        if ($cmp !== 0) return $cmp;

        $an = isset($a['professor']['name']) ? $a['professor']['name'] : '';
        $bn = isset($b['professor']['name']) ? $b['professor']['name'] : '';

        return $coll->compare($an, $bn);
    });

} else {
    // fallback: simple UTF-8 case-insensitive compare
    uasort($by_professor, function($a, $b) {
        $as = isset($a['professor']['surname']) ? $a['professor']['surname'] : '';
        $bs = isset($b['professor']['surname']) ? $b['professor']['surname'] : '';

        $as = function_exists('mb_strtolower') ? mb_strtolower($as, 'UTF-8') : strtolower($as);
        $bs = function_exists('mb_strtolower') ? mb_strtolower($bs, 'UTF-8') : strtolower($bs);

        $cmp = strcmp($as, $bs);
        if ($cmp !== 0) return $cmp;

        $an = isset($a['professor']['name']) ? $a['professor']['name'] : '';
        $bn = isset($b['professor']['name']) ? $b['professor']['name'] : '';

        $an = function_exists('mb_strtolower') ? mb_strtolower($an, 'UTF-8') : strtolower($an);
        $bn = function_exists('mb_strtolower') ? mb_strtolower($bn, 'UTF-8') : strtolower($bn);

        return strcmp($an, $bn);
    });
}
        return array(
            'examperiod'   => array('id' => $exid, 'label' => $examperiod_label),
            'by_professor' => $by_professor,
        );
    }

public function action_index()
{
    $examperiods = \Model_Examperiod::find('all');

    // Собираем годы (academic_year) -> label
    $years = array();
    $seasons_by_year = array(); // [year] => [season => label]
    foreach ($examperiods as $ep) {
        $y = (string)$ep->academic_year;
        $years[$y] = $y;

        if (!isset($seasons_by_year[$y])) {
            $seasons_by_year[$y] = array();
        }
        $seasons_by_year[$y][(string)$ep->season] = $ep->getGreekSeason($ep->season);
    }

    // selected filters
    $selected_year   = (string)\Input::get('year', '');
    if ($selected_year === '' || !isset($years[$selected_year])) {
        reset($years);
        $selected_year = key($years);
    }

    $selected_season = (string)\Input::get('season', '');
    if ($selected_season === '' || !isset($seasons_by_year[$selected_year][$selected_season])) {
        reset($seasons_by_year[$selected_year]);
        $selected_season = key($seasons_by_year[$selected_year]);
    }

    // Находим examperiod_id по выбранному year+season
    $selected_examperiod_id = null;
    foreach ($examperiods as $ep) {
        if ((string)$ep->academic_year === $selected_year && (string)$ep->season === $selected_season) {
            $selected_examperiod_id = (int)$ep->id;
            break;
        }
    }

    $data = array(
        'examperiods'           => $examperiods,
        'years'                 => $years,
        'seasons_by_year'       => $seasons_by_year,
        'selected_year'         => $selected_year,
        'selected_season'       => $selected_season,
        'selected_examperiod_id'=> $selected_examperiod_id,
    );

    $this->template->title = 'Assigner Emails';
    $this->template->content = \View::forge('admin/emails/assigner_emails_index', $data);
    return;
}


}
