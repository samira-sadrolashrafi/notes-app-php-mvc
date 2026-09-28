<?php

/**
 * @var array $data
 */

require_once APPROOT . '/views/layouts/header.php';

?>


<div class="container mt-5">


    <div class="row justify-content-center">


        <div class="col-md-6">


            <div class="card shadow border-0">


                <div class="card-body p-5">


                    <h3 class="text-center mb-4">

                        ویرایش یادداشت

                    </h3>



                    <form action="" method="POST" novalidate>



                        <div class="mb-3">


                            <label class="form-label">

                                عنوان یادداشت

                            </label>


                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                value="<?php echo $data['title']; ?>"
                            >


                            <small class="text-danger">

                                <?php echo $data['title_err']; ?>

                            </small>


                        </div>





                        <div class="mb-3">


                            <label class="form-label">

                                متن یادداشت

                            </label>


                            <textarea
                                name="body"
                                class="form-control"
                                rows="5"
                            ><?php echo $data['body']; ?></textarea>


                            <small class="text-danger">

                                <?php echo $data['body_err']; ?>

                            </small>


                        </div>





                        <div class="form-check mb-4">


                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_public"
                                value="1"
                                id="publicNote"
                                <?php echo ($data['is_public'] == 1) ? 'checked' : ''; ?>
                            >


                            <label
                                class="form-check-label"
                                for="publicNote"
                            >

                                انتشار عمومی یادداشت

                            </label>


                        </div>





                        <button
                            type="submit"
                            class="btn btn-secondary w-100"
                        >

                            ذخیره تغییرات

                        </button>


                    </form>



                </div>


            </div>


        </div>


    </div>


</div>



<?php

require_once APPROOT . '/views/layouts/footer.php';

?>