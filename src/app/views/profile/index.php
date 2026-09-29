<?php

/** @var array $data */
?>

<?php require_once APPROOT . '/views/layouts/header.php'; ?>


<div class="container mt-5 mb-5">


    <div class="row justify-content-center">

        <div class="col-md-5">


            <div class="card shadow">


                <div class="card-header bg-dark text-white">

                    <h5 class="mb-0">
                        پروفایل کاربری
                    </h5>

                </div>



                <div class="card-body">

                    <?php if (!empty($data['profile_err'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($data['profile_err'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">


                        <!-- Username -->

                        <div class="mb-3">

                            <label class="form-label">
                                نام کاربری
                            </label>


                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="<?= htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8') ?>">


                            <?php if (!empty($data['username_err'])): ?>

                                <div class="text-danger mt-1">
                                    <?= $data['username_err'] ?>
                                </div>

                            <?php endif; ?>


                        </div>




                        <!-- Email -->

                        <div class="mb-3">

                            <label class="form-label">
                                ایمیل
                            </label>


                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') ?>">


                            <?php if (!empty($data['email_err'])): ?>

                                <div class="text-danger mt-1">
                                    <?= $data['email_err'] ?>
                                </div>

                            <?php endif; ?>


                        </div>




                        <hr class="my-4">



                        <h6 class="mb-3">
                            تغییر رمز عبور
                        </h6>



                        <!-- Password -->

                        <div class="mb-3">

                            <label class="form-label">
                                رمز عبور جدید
                            </label>


                            <input
                                type="password"
                                name="password"
                                class="form-control">


                            <?php if (!empty($data['password_err'])): ?>

                                <div class="text-danger mt-1">
                                    <?= $data['password_err'] ?>
                                </div>

                            <?php endif; ?>


                        </div>




                        <!-- Password Confirm -->

                        <div class="mb-3">

                            <label class="form-label">
                                تکرار رمز عبور جدید
                            </label>


                            <input
                                type="password"
                                name="password_confirm"
                                class="form-control">


                            <?php if (!empty($data['password_confirm_err'])): ?>

                                <div class="text-danger mt-1">
                                    <?= $data['password_confirm_err'] ?>
                                </div>

                            <?php endif; ?>


                        </div>




                        <?php if (!empty($data['success'])): ?>

                            <div class="alert alert-success">
                                <?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?>
                            </div>


                        <?php endif; ?>


                        <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                ذخیره تغییرات
                            </button>

                            <a href="?page=notes" class="btn btn-outline-primary">
                                 بازگشت به صفحه یادداشت ها
                            </a>
                        </div>


                    </form>


                </div>


            </div>


        </div>


    </div>


</div>



<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
