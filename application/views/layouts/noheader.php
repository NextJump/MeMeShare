<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($headTitle) ? $headTitle : '' ?></title>
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.mobile-1.3.1.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/sitestyle.css">
    <script src="<?= base_url() ?>assets/js/jquery-1.9.1.min.js"></script>
    <script src="<?= base_url() ?>assets/js/jquery.mobile-1.3.1.min.js"></script>
    {css}
</head>
<body>
    <div id="container" data-role="page">
        <!-- Main Body -->
        <div class="page-content" data-role="content">
            {content}
        </div>
        
        <!-- Page Footer -->
        <div class="page-footer"></div>
    </div>

    {js}
</body>
</html>
