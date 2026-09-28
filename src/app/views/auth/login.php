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
                        ورود
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


                        <div class="form-check mb-3">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="remember"
                                id="remember"
                                value="1">

                            <label
                                class="form-check-label"
                                for="remember">

                                مرا به خاطر بسپار

                            </label>

                        </div>

                        <button type="submit" class="btn btn-primary w-100">

                            ورود

                        </button>



                    </form>




                    <div class="text-center mt-4">


                        <span>
                            حساب کاربری ندارید؟
                        </span>


                        <a
                            href="?page=register"
                            class="text-decoration-none">

                            ثبت نام کنید

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