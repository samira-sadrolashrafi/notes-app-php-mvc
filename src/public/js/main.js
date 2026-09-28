document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.jalali-date[data-date]').forEach((dateElement) => {
        const date = moment(dateElement.dataset.date);

        if (date.isValid()) {
            dateElement.textContent = date
                .locale('en')
                .format('jYYYY/jMM/jDD');
        }
    });


    // =====================================================
    // Modal ایجاد یادداشت
    // =====================================================

    const createModal = document.getElementById('createNoteModal');

    if (createModal?.dataset.open === 'true') {
        bootstrap.Modal.getOrCreateInstance(createModal).show();
    }



    // =====================================================
    // پاک کردن خطاهای فرم
    // =====================================================

    const clearFormErrors = (form) => {

        form.querySelectorAll('[data-error-for]').forEach((errorElement) => {

            errorElement.textContent = '';
            errorElement.classList.add('d-none');

        });


        const requestError = form.querySelector('[data-request-error]');

        if (requestError) {
            requestError.textContent = '';
        }

    };



    // =====================================================
    // پاک کردن خطاها هنگام باز و بسته شدن Modal
    // =====================================================

    document.querySelectorAll('.note-form-modal').forEach((modal) => {

        modal.addEventListener('hidden.bs.modal', () => {

            const form = modal.querySelector('form');

            if (form) {
                clearFormErrors(form);
            }

        });


        modal.addEventListener('show.bs.modal', () => {

            const form = modal.querySelector('form');

            if (form) {
                clearFormErrors(form);
            }

        });

    });



    // =====================================================
    // ارسال فرم ایجاد و ویرایش یادداشت با AJAX
    // =====================================================

    document.querySelectorAll('.note-form').forEach((form) => {

        form.addEventListener('submit', async (event) => {

            event.preventDefault();


            const submitButton = form.querySelector('[type="submit"]');

            submitButton.disabled = true;


            const requestError = form.querySelector('[data-request-error]');

            if (requestError) {
                requestError.textContent = '';
            }


            try {

                const response = await fetch(form.action, {

                    method: 'POST',

                    body: new FormData(form),

                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }

                });

 
                const result = await response.json();


                ['title', 'body'].forEach((field) => {

                    const errorElement = form.querySelector(
                        `[data-error-for="${field}"]`
                    );

                    const error = result[`${field}_err`];


                    if (errorElement) {

                        errorElement.textContent = error || '';

                        errorElement.classList.toggle(
                            'd-none',
                            !error
                        );

                    }

                });


                if (result.success) {

                    window.location.reload();

                } else if (
                    !result.title_err &&
                    !result.body_err &&
                    requestError
                ) {

                    requestError.textContent =
                        'ذخیره تغییرات انجام نشد. دوباره تلاش کنید.';

                }


            } catch (error) {

                console.error(
                    'ثبت یادداشت انجام نشد.',
                    error
                );


                if (requestError) {

                    requestError.textContent =
                        'ارتباط با سرور ناموفق بود. دوباره تلاش کنید.';

                }


            } finally {

                submitButton.disabled = false;

            }

        });

    });



    // =====================================================
    // Persian Datepicker
    // =====================================================

    const fromDateDisplay = $('#fromDateDisplay');

    const toDateDisplay = $('#toDateDisplay');

    const fromDate = $('#fromDate');

    const toDate = $('#toDate');


    // اگر فیلدهای تاریخ در صفحه وجود نداشتند،
    // کد Datepicker اجرا نمی‌شود
    if (
        fromDateDisplay.length &&
        toDateDisplay.length
    ) {


        // =================================================
        // تبدیل Unix Timestamp به تاریخ میلادی
        // =================================================

        const unixToGregorian = (unix) => {

            return new persianDate(unix)
                .toCalendar('gregorian')
                .toLocale('en')
                .format('YYYY-MM-DD');

        };


        // =================================================
        // تبدیل تاریخ میلادی به Unix Timestamp
        // =================================================

        const gregorianToUnix = (dateString) => {

            if (!dateString) {
                return null;
            }


            const parts = dateString
                .split('-')
                .map(Number);


            if (
                parts.length !== 3 ||
                parts.some(Number.isNaN)
            ) {
                return null;
            }


            const year = parts[0];
            const month = parts[1];
            const day = parts[2];


            return new Date(
                year,
                month - 1,
                day,
                12,
                0,
                0
            ).getTime();

        };



        // =================================================
        // تنظیمات مشترک دو تقویم
        // =================================================

        const commonOptions = {

            calendarType: 'persian',

            initialValue: false,

            format: 'YYYY/MM/DD',

            autoClose: true,

            observer: false,

            onlySelectOnDate: true,


            calendar: {

                persian: {

                    locale: 'fa',

                    showHint: false,

                    leapYearMode: 'algorithmic'

                },


                gregorian: {

                    locale: 'en',

                    showHint: false

                }

            },


            navigator: {

                enabled: true,

                scroll: {

                    enabled: false

                }

            },


            toolbox: {

                calendarSwitch: {

                    enabled: false

                }

            },


            timePicker: {

                enabled: false

            }

        };



        let fromPicker;

        let toPicker;



        // =================================================
        // Datepicker مربوط به "تا تاریخ"
        // =================================================

        toPicker = toDateDisplay.persianDatepicker({

            ...commonOptions,


            onSelect: function (unix) {

                // این Datepicker توسط کاربر انتخاب شده
                toPicker.touched = true;


                // تبدیل تاریخ انتخاب‌شده به میلادی
                const gregorianDate =
                    unixToGregorian(unix);


                // ذخیره مقدار میلادی در Hidden Input
                toDate.val(gregorianDate);


                // اگر "تا تاریخ" انتخاب شد،
                // "از تاریخ" نباید بعد از آن باشد
                if (
                    fromPicker &&
                    fromPicker.options &&
                    fromPicker.options.maxDate !== unix
                ) {

                    const state =
                        fromPicker.getState();


                    const cachedValue =
                        state?.selected?.unixDate;


                    fromPicker.options = {

                        maxDate: unix

                    };


                    // اگر From قبلاً انتخاب شده بود،
                    // مقدار قبلی‌اش حفظ شود
                    if (
                        fromPicker.touched &&
                        cachedValue
                    ) {

                        fromPicker.setDate(
                            cachedValue
                        );

                    }

                }

            }

        });



        // =================================================
        // Datepicker مربوط به "از تاریخ"
        // =================================================

        fromPicker = fromDateDisplay.persianDatepicker({

            ...commonOptions,


            onSelect: function (unix) {

                // این Datepicker توسط کاربر انتخاب شده
                fromPicker.touched = true;


                // تبدیل تاریخ انتخاب‌شده به میلادی
                const gregorianDate =
                    unixToGregorian(unix);


                // ذخیره مقدار میلادی در Hidden Input
                fromDate.val(gregorianDate);


                // اگر "از تاریخ" انتخاب شد،
                // "تا تاریخ" نباید قبل از آن باشد
                if (
                    toPicker &&
                    toPicker.options &&
                    toPicker.options.minDate !== unix
                ) {

                    const state =
                        toPicker.getState();


                    const cachedValue =
                        state?.selected?.unixDate;


                    toPicker.options = {

                        minDate: unix

                    };


                    // اگر To قبلاً انتخاب شده بود،
                    // مقدار قبلی‌اش حفظ شود
                    if (
                        toPicker.touched &&
                        cachedValue
                    ) {

                        toPicker.setDate(
                            cachedValue
                        );

                    }

                }

            }

        });



        // =================================================
        // بازیابی تاریخ‌های قبلی بعد از Reload صفحه
        // =================================================

        const currentFromDate =
            fromDate.val();


        const currentToDate =
            toDate.val();


        const currentFromUnix =
            gregorianToUnix(
                currentFromDate
            );


        const currentToUnix =
            gregorianToUnix(
                currentToDate
            );



        // -------------------------------------------------
        // بازیابی "از تاریخ"
        // -------------------------------------------------

        if (currentFromUnix !== null) {

            fromPicker.touched = true;

            fromPicker.setDate(
                currentFromUnix
            );

        }



        // -------------------------------------------------
        // بازیابی "تا تاریخ"
        // -------------------------------------------------

        if (currentToUnix !== null) {

            toPicker.touched = true;

            toPicker.setDate(
                currentToUnix
            );

        }



        // -------------------------------------------------
        // اعمال محدودیت From روی To
        // -------------------------------------------------

        if (currentFromUnix !== null) {

            toPicker.options = {

                minDate: currentFromUnix

            };


            if (currentToUnix !== null) {

                toPicker.setDate(
                    currentToUnix
                );

            }

        }



        // -------------------------------------------------
        // اعمال محدودیت To روی From
        // -------------------------------------------------

        if (currentToUnix !== null) {

            fromPicker.options = {

                maxDate: currentToUnix

            };


            if (currentFromUnix !== null) {

                fromPicker.setDate(
                    currentFromUnix
                );

            }

        }



        // =================================================
        // پاک کردن "از تاریخ"
        // =================================================

        $('#clearFromDate').on(
            'click',
            function () {

                // مقدار نمایشی
                fromDateDisplay.val('');


                // مقدار میلادی Backend
                fromDate.val('');


                // دیگر From انتخاب نشده
                fromPicker.touched = false;


                // To دیگر محدودیت حداقل تاریخ ندارد
                toPicker.options = {

                    minDate: null

                };

            }
        );



        // =================================================
        // پاک کردن "تا تاریخ"
        // =================================================

        $('#clearToDate').on(
            'click',
            function () {

                // مقدار نمایشی
                toDateDisplay.val('');


                // مقدار میلادی Backend
                toDate.val('');


                // دیگر To انتخاب نشده
                toPicker.touched = false;


                // From دیگر محدودیت حداکثر تاریخ ندارد
                fromPicker.options = {

                    maxDate: null

                };

            }
        );

    }

});
