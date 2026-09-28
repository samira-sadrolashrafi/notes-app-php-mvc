<?php

/**
 * @var array $data
 */

require_once APPROOT . '/views/layouts/header.php';

?>


<div class="container">

    <div class="row justify-content-center align-items-center vh-100">


        <div class="col-md-5">


            <div class="card shadow border-0">


                <div class="card-body p-5">


                    <h3 class="text-center mb-4">
                        ثبت نام
                    </h3>



                    <form
                        action=""
                        method="POST"
                        novalidate
                        autocomplete="off">



                        <div class="mb-3">


                            <label class="form-label">
                                نام کاربری
                            </label>


                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="<?php echo $data['username']; ?>"
                                autocomplete="off">


                            <small class="text-danger">
                                <?php echo $data['username_err']; ?>
                            </small>


                        </div>




                        <div class="mb-3">


                            <label class="form-label">
                                ایمیل
                            </label>


                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?php echo $data['email']; ?>"
                                autocomplete="off">


                            <small class="text-danger">
                                <?php echo $data['email_err']; ?>
                            </small>


                        </div>




                        <div class="mb-3">


                            <label class="form-label">
                                رمز عبور
                            </label>


                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                autocomplete="new-password">


                            <small class="text-danger">
                                <?php echo $data['password_err']; ?>
                            </small>


                        </div>




                        <div class="mb-3">


                            <label class="form-label">
                                تکرار رمز عبور
                            </label>


                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                autocomplete="new-password">


                            <small class="text-danger">
                                <?php echo $data['confirm_password_err']; ?>
                            </small>


                        </div>




                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            ثبت نام

                        </button>



                    </form>




                    <div class="text-center mt-4">


                        <span>
                            قبلاً ثبت نام کرده‌اید؟
                        </span>


                        <a
                            href="?page=login"
                            class="text-decoration-none">

                            وارد شوید

                        </a>


                    </div>



                </div>


            </div>


        </div>


    </div>


</div>



<?php

require_once APPROOT . '/views/layouts/footer.php';

?>