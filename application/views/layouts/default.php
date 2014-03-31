<?php
    $controller = $this->router->fetch_class();
    $action = $this->router->fetch_method();
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.mobile-1.3.1.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/sitestyle.css">
    <script src="<?= base_url() ?>assets/js/jquery-1.9.1.min.js"></script>
    <script src="<?= base_url() ?>assets/js/jquery.mobile-1.3.1.min.js"></script>
</head>
<body>
    <div id="container-<?= $controller ?><?= (isset($this->containerIdentifier) && $this->containerIdentifier) ? ('-' . $this->containerIdentifier) : '' ?>" data-role="page">
        {css}
        
        <!-- Menu Panel -->
        <div id="navmenu" data-role="panel" data-theme="none" data-display="reveal">
            <ul data-role="listview" data-theme="none" data-icon="false">
                <li <?= $this->menuItem == 'Home' ? 'class="active"' : '' ?>>
                    <a href="<?= base_url() ?>home/index">
                        <i class="menu-icon home"></i>
                        <h2>My House</h2>
                    </a>
                </li>
                <li <?= $this->menuItem == 'Family' ? 'class="active"' : '' ?>>
                    <a href="<?= base_url() ?>home/index/familyid/<?= array_key_exists('familyid', $this->user->family) ? $this->user->family['familyid'] : 0 ?>">
                        <i class="menu-icon family"></i>
                        <h2>My Family</h2>
                    </a>
                </li>
                <li <?= $this->menuItem == 'Profile' ? 'class="active"' : '' ?>>
                    <a href="<?= base_url() ?>profile/index/id/<?= $this->user->id ?>">
                        <i class="menu-icon profile-male"></i>
                        <h2>Profile</h2>
                    </a>
                </li>
                <li <?= $this->menuItem == 'Log' ? 'class="active"' : '' ?>>
                    <a href="<?= base_url() ?>interaction/log">
                        <i class="menu-icon log"></i>
                        <?php if ($this->agent->is_mobile('iphone') || $this->agent->is_mobile('ipad') || $this->agent->is_mobile('ipod')) { ?>
                        <h2>Log</h2>
                        <?php } else { ?>
                        <h2>Log Interaction</h2>
                        <?php } ?>
                    </a>
                </li>
                <?php if ((int)$this->user->is_admin === 1) { ?>
                <li <?= $this->menuItem == 'Admin' ? 'class="active"' : '' ?>>
                    <a href="<?= base_url() ?>admin">
                        <i class="menu-icon admin"></i>
                        <h2>Admin</h2>
                    </a>
                </li>
                <?php } ?>
                <li class="menu-btn-logout">
                    <a href="<?= base_url() ?>login/logout" data-ajax="false">
                        <h2>Logout</h2>
                        <p class="muted"><span class="light">Signed in as:</span><br /><?= $this->user->fname . ' ' . substr($this->user->lname, 0, 1) . '.' ?></p>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Page Header -->
        <div class="page-header" data-role="header" data-theme="imp" data-position="fixed" data-tap-toggle="false">
            <h1><?= isset($this->pageTitle) ? $this->pageTitle : '' ?></h1>
            <h2><?= isset($this->pageSubtitle) ? $this->pageSubtitle : '' ?></h2>
            <a href="#navmenu" class="navmenu-link"><i class="menu-icon bars"></i></a>
        </div>
        
        <!-- Main Body -->
        <div class="page-content" data-role="content">
            {content}
        </div>
        
        <?php if (!($controller == 'interaction' && $action == 'log') && ($controller !== 'admin')) { ?>
        <!-- Page Footer -->
        <div class="page-footer" data-role="footer" data-position="fixed" data-theme="none" data-tap-toggle="false">
            <a href="/interaction/log" class="log-interaction-button active" data-corners="false"><h1><i class="menu-icon log" style="float: none;"></i>Log Your Interaction</h1></a>
        </div>
        <?php } ?>
        
        {js}
    </div>
    <script type="text/javascript">
        // Add a listener for when the Home page loads
        $(document).on('pageinit', '#container-home', function() {
            var container = 'container-home';
            imp.home.index.setup(container);
            
            if (!$.support.placeholder()) {
                enablePlaceholders(container);
            }
        });

        // Add a listener for when the My Student page loads
        $(document).on('pageinit', '#container-home-family', function() {
            var container = 'container-home-family';
            imp.home.index.setup(container);
            
            if (!$.support.placeholder()) {
                enablePlaceholders(container);
            }
        });
        
        // Add a listener for when the Profile page loads
        $(document).on('pageinit', '#container-profile', function() {
            var container = 'container-profile';
            imp.profile.index.setup(container);

            if (!$.support.placeholder()) {
                enablePlaceholders(container);
            }
        });
        
        // Add a listener for when the Log Interaction page loads
        $(document).on('pageinit', '#container-interaction', function(evt) {
            imp.interaction.setup();
            
            if (!$.support.placeholder()) {
                enablePlaceholders(container);
            }
        });
        
        // Some listening for browsers that don't support placeholders
        // Placeholder listening
        jQuery.support.placeholder = (function() {
            var i = document.createElement('input');
            return 'placeholder' in i;
        });
        
        function enablePlaceholders(container) {
            $('#' + container + ' input[placeholder]').each(function(i, ele) {
                var ele = $(ele),
                    placeholderText = ele.attr('placeholder');
                
                // Listen for focus events
                ele.off('focus');
                ele.focus(function(evt) {
                    // Clear out anything in the input
                    if (ele.val() == placeholderText) {
                        ele.val('');
                    }
                });
                
                // Listen for blur events
                ele.off('blur');
                ele.blur(function(evt) {
                    // Make the input look like a placeholder
                    if (ele.val() == '') {
                        ele.val(placeholderText);
                    }
                });
                
                // Initialize the input
                if (ele.val() == '') {
                    ele.siblings('a.ui-input-clear').each(function(ind, e) {
                        $(e).addClass('ui-input-clear-hidden');
                    });
                    ele.val(placeholderText);
                }
            });
        }
    </script>
</body>
</html>
