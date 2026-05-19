<aside class="admin-sidebar">
    <ul class="sidebar-nav">

        <li class="sidebar-group-title">SETUP</li>

        <?php if (Auth::has_access('courses.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/courses'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/courses'); ?>">Courses</a>
            </li>
        <?php endif; ?>

        <?php if (Auth::has_access('professors.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/professors'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/professors'); ?>">Professors</a>
            </li>
        <?php endif; ?>

        <?php if (Auth::has_access('doctorals.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/doctorals', false); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/doctorals'); ?>">Doctorals</a>
            </li>
        <?php endif; ?>

        <li class="sidebar-divider"></li>

        <li class="sidebar-group-title">EXAMS</li>

        <?php if (Auth::has_access('examperiods.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/examperiods'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/examperiods'); ?>">Exam Periods</a>
            </li>
        <?php endif; ?>

        <?php if (Auth::has_access('examsupervisions.read')): ?>
        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/examsupervisions'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/examsupervisions'); ?>">Exam Supervisions</a>
        </li>
        <?php endif; ?>


        <?php if (Auth::has_access('availability.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/availability'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/availability'); ?>">Supervisors' Availability</a>
            </li>
        <?php endif; ?>

        <li class="sidebar-divider"></li>
        
        <li class="sidebar-group-title">ASSIGNMENT</li>

        <?php if (Auth::has_access('assigner.read')): ?>
        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/assigner'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/assigner'); ?>">Assign Doctorals</a>
        </li>

        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/assigner_emails'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/assigner_emails'); ?>">Assigner Emails</a>
        </li>
        <?php endif; ?>

        <li class="sidebar-divider"></li>


        <li class="sidebar-group-title">EMAILS</li>

        <?php if (Auth::has_access('emails.read')): ?>
            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/emails'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/emails'); ?>">Listing Emails</a>
            </li>

            <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/custom/emails'); ?>>
                <a class="sidebar-link" href="<?php echo Uri::create('admin/custom/emails'); ?>">Custom Emails</a>
            </li>
            
        <?php endif; ?>

        <li class="sidebar-divider"></li>

        <li class="sidebar-group-title">REPORTS</li>

        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/reports/examperiod_noncompliance'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/reports/examperiod_noncompliance'); ?>">
                Non-compliance Report
            </a>
        </li>

        
        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/doctorals/not_responded'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/doctorals/not_responded'); ?>">
                Not Responded
            </a>
        </li>
        
        <li class="sidebar-divider"></li>

        <li class="sidebar-group-title">USERS</li>

        <li <?php \Controller_Admin::echoActiveClassIfRequestMatches('admin/users'); ?>>
            <a class="sidebar-link" href="<?php echo Uri::create('admin/users'); ?>">
                App Users
            </a>
        </li>

    </ul>
</aside>
