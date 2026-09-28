<?php

/**
 * @var array $data
 */

require_once APPROOT . '/views/layouts/header.php';

?>


<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body text-center">


            <h3>
                خوش آمدید
                <?php echo $_SESSION['user_name']; ?>
            </h3>


            <p class="mt-3">
                شما با موفقیت وارد شدید.
            </p>

            <a
                href="?page=logout"
                class="btn btn-danger mt-3">

                خروج

            </a>


        </div>

    </div>

</div>


<?php

require_once APPROOT . '/views/layouts/footer.php';

?>