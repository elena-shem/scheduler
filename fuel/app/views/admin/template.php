<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></title>

    <script>
        window.APP_BASE = "<?php echo rtrim(Uri::base(), '/'); ?>";
    </script>

    <?php
    echo Asset::css(array(
        'vendor/normalize.css',
        'flex.css',
        'bootstrap-flatly-theme.min.css',
        'admin-template.css',
        'vendor/select2/select2.min.css',
        'modern-admin.css',
    ));

    if (isset($css)) {
        echo Asset::css($css);
    }


    echo Asset::js(array(
    'vendor/jquery/jquery-1.11.1.min.js',
    'vendor/bootstrap.min.js',
    ));
    echo Asset::js(array(
    'vendor/bootbox.min.js',
    'vendor/select2/select2.min.js',
    'table-multifilter.js',
    'template/main.js',
    ), array('defer' => 'defer'));

    if (isset($js)) {
        echo Asset::js($js, array('defer' => 'defer'));
    }
    ?>
</head>

<body>
    
<?php if (isset($current_user)): ?>
  <div id="stickyHScroll" class="sticky-hscroll">
    <div class="sticky-hscroll__inner"></div>
  </div>
<?php endif; ?>


<div id="wrap">

<?php if (isset($current_user)): ?>

    <?php echo $navigation; ?>

    <div class="admin-layout">
        <?php echo View::forge('admin/_sidebar'); ?>

        <div class="admin-content">
            <div class="container">
                <div class="row">

                    <div class="col-md-12">
                        <div class="page-header">
                            <h1 class="page-title"><?php echo htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></h1>
                        </div>

                        <?php if (Session::get_flash('success')): ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p><?php echo implode('</p><p>', (array) Session::get_flash('success')); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p><?php echo implode('</p><p>', (array) Session::get_flash('error')); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <div class="content-card">
                            <?php echo $content; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <footer class="app-footer">
            <div class="container">
                <div class="footer-row">
                    <span class="text-muted">Server date and time: <i id="serverTime"></i></span>
                    <span class="text-muted">Page rendered in {exec_time}s using {mem_usage}mb of memory.</span>
                    <span class="text-muted">
                    <a href="#" data-toggle="modal" data-target="#aboutModal">About</a>
                    </span>
                </div>
            </div>
        </footer>

    </div>

<?php else: ?>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo Uri::create('/'); ?>">DI Exam Scheduler</a>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:70px;">
        <div class="content-card">
            <?php echo $content; ?>
        </div>
    </div>

<?php endif; ?>

</div> <!-- #wrap -->


<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="aboutModalLabel">About</h4>
      </div>

      <div class="modal-body">

        <div class="row">
          <div class="col-sm-12">
            <h4><strong>Σχεδίαση &amp; Υλοποίηση Εφαρμογής</strong></h4>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-offset-1 col-sm-11">Βλάχος Σπυρίδων</div>
        </div>
        <div class="row">
          <div class="col-sm-offset-1 col-sm-11">Παθούλας Πέτρος</div>
        </div>

        <hr>

        <div class="row">
          <div class="col-sm-12">
            <h4><strong>Επίβλεψη</strong></h4>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-offset-1 col-sm-11">
            <div class="row">
              <a href="http://www.di.uoa.gr/staff/411" target="_blank" rel="noopener">Κατσιάνης Δημήτριος</a>
            </div>
            <div class="row"><strong>Τομέας:</strong> Επικοινωνίες και Επεξεργασία Σήματος</div>
            <div class="row">
              <strong>Τηλέφωνο:</strong> <a href="tel:+302107275319">+30 210 727 5319</a><br>
              <strong>Fax:</strong> +30 210 727 5319
            </div>
            <div class="row"><strong>Γραφείο:</strong> Α25</div>
            <div class="row"><strong>E-mail:</strong> dkats[AT]di.uoa.gr</div>
          </div>
        </div>

        <br>

        <div class="row">
          <div class="col-sm-offset-1 col-sm-11">
            <div class="row">
              <a href="http://www.di.uoa.gr/staff/389" target="_blank" rel="noopener">Σταματόπουλος Παναγιώτης</a>
            </div>
            <div class="row"><strong>Βαθμίδα:</strong> Επίκουρος Καθηγητής</div>
            <div class="row"><strong>Τομέας:</strong> Υπολογιστικά Συστήματα και Εφαρμογές</div>
            <div class="row">
              <strong>Τηλέφωνο:</strong> <a href="tel:+302107275202">+30 210 727 5202</a>
            </div>
            <div class="row"><strong>Γραφείο:</strong> A48</div>
            <div class="row"><strong>E-mail:</strong> takis[AT]di.uoa.gr</div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<script>
  (function () {
    if (!window.jQuery) return;

    jQuery(document).on('click', 'a[data-target="#aboutModal"]', function (e) {
      e.preventDefault();
      var $m = jQuery('#aboutModal');
      if ($m.length && $m.modal) $m.modal('show');
    });
  })();
</script>

</body>
</html>
