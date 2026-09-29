<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="data:,">

    <title>Notes App</title>


    <!-- Bootstrap RTL -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet">


    <!-- Persian Datepicker -->
    <link
        rel="stylesheet"
        href="<?php echo URLROOT; ?>/public/vendor/persian-datepicker/persian-datepicker.min.css">

    <!-- CSS پروژه -->
    <link
        rel="stylesheet"
        href="<?php echo URLROOT; ?>/public/css/style.css?v=3">

</head>


<body class="bg-light">


    <nav class="navbar navbar-dark bg-dark">

        <div class="container">


            <span class="navbar-brand mb-0 h1">
                Notes App
            </span>


            <?php if (isLoggedIn()): ?>

                <div class="d-flex align-items-center gap-3 ms-auto">

                    <a
                        href="?page=profile"
                        class="text-white text-decoration-none">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </a>


                    <a
                        href="?page=logout"
                        class="btn btn-outline-light btn-sm">
                        خروج
                    </a>

                </div>


            <?php endif; ?>


        </div>

    </nav>