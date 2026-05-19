<?php

class Model_Report extends Model
{
    /**
     * Returns doctoral records (with their supervisors) who:
     * - have an assignments_preferences entry for the given exam period
     * - but have NO records in preferencesavailable
     *   (i.e. they did NOT respond to the availability declaration).
     *
     * @param int $examperiod_id
     * @return array
     */
    public static function no_response_to_availability($examperiod_id)
    {
        $examperiod_id = (int)$examperiod_id;

        $sql = "
            SELECT
              ds.professor_id,
              p.surname AS professor_surname,
              p.name AS professor_name,
              d.id AS doctoral_id,
              d.surname AS doctoral_surname,
              d.name AS doctoral_name,
              d.email AS doctoral_email
            FROM doctoralsupervisors ds
            JOIN professors p ON p.id = ds.professor_id
            JOIN doctorals d ON d.id = ds.doctoral_id
            JOIN assignments_preferences ap
              ON ap.doctoral_id = ds.doctoral_id
             AND ap.examperiod_id = :examperiod_id
            LEFT JOIN preferencesavailable pa
              ON pa.doctoral_id = ds.doctoral_id
             AND pa.examperiod_id = :examperiod_id
            WHERE pa.id IS NULL
            ORDER BY ds.professor_id, d.surname, d.name
        ";

        return DB::query($sql)
            ->bind('examperiod_id', $examperiod_id)
            ->execute()
            ->as_array();
    }

    /**
     * Returns doctoral records (with their supervisors) who:
     * - belong to the given exam period
     * - have at least one exam supervision with attended = 0
     *   (i.e. they did NOT attend a scheduled supervision).
     *
     * @param int $examperiod_id
     * @return array
     */
    public static function no_show_on_supervision($examperiod_id)
    {
        $examperiod_id = (int)$examperiod_id;

        $sql = "
            SELECT
            ds.professor_id,
            p.surname AS professor_surname,
            p.name AS professor_name,

            d.id AS doctoral_id,
            d.surname AS doctoral_surname,
            d.name AS doctoral_name,
            d.email AS doctoral_email,

            es.examcourse_id,

            ec.course_id,
            c.code  AS course_code,
            c.title AS course_title,

            ed.day   AS exam_day,
            eh.start AS exam_start,
            eh.end   AS exam_end

            FROM exam_supervisions es
            JOIN examcourses ec ON ec.id = es.examcourse_id

            JOIN courses c ON c.id = ec.course_id
            JOIN examdays ed ON ed.id = ec.examday_id
            JOIN examhours eh ON eh.id = ec.examhour_id

            JOIN doctoralsupervisors ds ON ds.doctoral_id = es.doctoral_id
            JOIN professors p ON p.id = ds.professor_id
            JOIN doctorals d ON d.id = es.doctoral_id

            WHERE ec.examperiod_id = :examperiod_id
            AND es.attended = 0

            ORDER BY ds.professor_id, ed.day, eh.start, d.surname, d.name, es.examcourse_id
        ";

        return DB::query($sql)
            ->bind('examperiod_id', $examperiod_id)
            ->execute()
            ->as_array();
    }

    /**
     * Builds the final report structure for an exam period:
     *
     *   professor_id => [
     *       professor_id,
     *       professor_name,
     *       no_response => [doctoral_id => {...}],
     *       no_show     => [doctoral_id => { events: [...] }]
     *   ]
     *
     * This structure is ready to be consumed directly by the view layer.
     *
     * @param int $examperiod_id
     * @return array
     */
    public static function examperiod_noncompliance($examperiod_id)
    {
        // Fetch raw rows for both violation categories
        $no_response_rows = static::no_response_to_availability($examperiod_id);
        $no_show_rows     = static::no_show_on_supervision($examperiod_id);

        $report = array();

        /**
         * Ensures that a professor entry exists in the report structure.
         * Returns the professor_id.
         */
        $ensure_prof = function($row) use (&$report) {
            $pid = (int)$row['professor_id'];

            if (!isset($report[$pid]))
            {
                $report[$pid] = array(
                    'professor_id'   => $pid,
                    'professor_name' => $row['professor_surname'] . ' ' . $row['professor_name'],
                    'no_response'    => array(), // doctoral_id => basic info
                    'no_show'        => array(), // doctoral_id => supervision events
                );
            }

            return $pid;
        };

        // Process "no response to availability" violations
        foreach ($no_response_rows as $r)
        {
            $pid = $ensure_prof($r);
            $did = (int)$r['doctoral_id'];

            // Prevent duplicates
            if (!isset($report[$pid]['no_response'][$did]))
            {
                $report[$pid]['no_response'][$did] = array(
                    'doctoral_id'    => $did,
                    'doctoral_name'  => $r['doctoral_surname'] . ' ' . $r['doctoral_name'],
                    'doctoral_email' => $r['doctoral_email'],
                );
            }
        }

        // Process "no show on supervision" violations
        foreach ($no_show_rows as $r)
        {
            $pid = $ensure_prof($r);
            $did = (int)$r['doctoral_id'];

            if (!isset($report[$pid]['no_show'][$did]))
            {
                $report[$pid]['no_show'][$did] = array(
                    'doctoral_id'    => $did,
                    'doctoral_name'  => $r['doctoral_surname'] . ' ' . $r['doctoral_name'],
                    'doctoral_email' => $r['doctoral_email'],
                    'events'         => array(),
                );
            }

            // Each no-show may include multiple supervision events
            $report[$pid]['no_show'][$did]['events'][] = array(
                'examcourse_id' => (int)$r['examcourse_id'], // можно не выводить, но оставить для отладки
                'course_code'   => $r['course_code'],
                'course_title'  => $r['course_title'],
                'exam_day'      => $r['exam_day'],
                'exam_start'    => $r['exam_start'],
                'exam_end'      => $r['exam_end'],
            );

        }

        return $report;
    }
}
