<?php

class ProfileController extends Controller
{
    private $userModel;


    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $userId = (int) $_SESSION['user_id'];

        $user = $this->userModel->getUserById($userId);


        if (!$user) {
            redirect('logout');
        }


        $data = [

            'username' => $user->username,
            'email' => $user->email,
            'password' => '',
            'password_confirm' => '',

            'username_err' => '',
            'email_err' => '',
            'password_err' => '',
            'password_confirm_err' => '',
            'profile_err' => '',
            'success' => '',
        ];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $readPostString = static function ($key) {
                return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
            };


            $data['username'] = $readPostString('username');

            $data['email'] = $readPostString('email');

            $data['password'] = $readPostString('password');

            $data['password_confirm'] = $readPostString('password_confirm');

            $data['username_err'] = validateUsername($data['username']);


            if (empty($data['username_err'])) {

                $existingUsername = $this->userModel->findUserByUsernameExceptId(
                        $data['username'],
                        $userId
                    );

                if ($existingUsername) {
                    $data['username_err'] =
                        'این نام کاربری قبلاً استفاده شده است.';
                }

            }


            $data['email_err'] =
                validateEmail($data['email']);


            if (empty($data['email_err'])) {

                $existingEmail =
                    $this->userModel->findUserByEmailExceptId(
                        $data['email'],
                        $userId
                    );

                if ($existingEmail) {
                    $data['email_err'] =
                        'این ایمیل قبلاً استفاده شده است.';

                }

            }


            if ($data['password'] !== '') {


                $data['password_err'] = validatePassword($data['password']);



                if ($data['password_confirm'] === '') {

                    $data['password_confirm_err'] =
                        'لطفاً تکرار رمز عبور را وارد کنید.';

                } elseif ($data['password'] !== $data['password_confirm'])
                {
                    $data['password_confirm_err'] =
                        'رمز عبور و تکرار آن یکسان نیست.';
                }


            } elseif ($data['password_confirm'] !== '') {

                $data['password_confirm_err'] =
                    'برای تأیید رمز، ابتدا رمز عبور جدید را وارد کنید.';
            }

            $hasErrors = $data['username_err'] !== '' || $data['email_err'] !== '' || $data['password_err'] !== '' || $data['password_confirm_err'] !== '';

            if (!$hasErrors) {

                $profileData = [
                    'id' => $userId,
                    'username' => $data['username'],
                    'email' => $data['email']
                ];

                if ($this->userModel->updateProfile($profileData)) {

                    $_SESSION['user_name'] = $data['username'];

                    $passwordUpdated = true;

                    if ($data['password'] !== '') {
                        $passwordUpdated =
                            $this->userModel->updatePassword(
                                $userId,
                                $data['password']
                            );
                    }

                    if ($passwordUpdated) {
                        $data['success'] =
                            'اطلاعات پروفایل با موفقیت به‌روزرسانی شد.';
                    } else {
                        $data['profile_err'] =
                            'پروفایل به‌روزرسانی شد، اما تغییر رمز عبور انجام نشد.';
                    }

                } else {
                    $data['profile_err'] =
                        'به‌روزرسانی اطلاعات پروفایل انجام نشد.';
                }
            }
        }

        $this->view('profile/index', $data);
    }
}