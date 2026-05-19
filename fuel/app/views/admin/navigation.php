<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">

        <div class="navbar-header">

        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
            <span class="st-bar"></span>
            <span class="st-bar"></span>
            <span class="st-bar"></span>
        </button>

            <!-- Bootstrap collapse toggle (for mobile navbar menu) -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand brand" href="<?php echo Uri::create('admin'); ?>">
                <span class="brand-text">DI Exam Scheduler</span>
            </a>


        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <?php echo e($current_user->username); ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php if (Auth::has_access('global_settings.read')): echo Html::anchor('admin/globalsettings', 'Global Settings'); endif; ?></li>
                        <li><?php echo Html::anchor('admin/users/edit_self', 'Edit Profile'); ?></li>
                        <li><?php echo Html::anchor('admin/logout', 'Logout'); ?></li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</div>
