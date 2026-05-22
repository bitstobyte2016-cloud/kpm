<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        <?= $page_title ?? 'K-Pop Merch'; ?>
    </title>

    <link 
        rel="icon"
        type="image/png"
        href="<?= base_url('assets/images/logo.png'); ?>"
    >


    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet"
          href="<?= base_url('css/theme.css'); ?>">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>

<body>

    <!-- HEADER -->

    <?= view('layouts/header'); ?>


    <!-- PAGE CONTENT -->

    <?= $this->renderSection('content'); ?>


    <!-- FOOTER -->

    <?= view('layouts/footer'); ?>

</body>

</html>