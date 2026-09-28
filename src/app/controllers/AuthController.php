<?php

class AuthController extends Controller
{

    private $userModel;

    public function __construct()
    {

        $this->userModel = new User();
    }

    public function register()
    {
        if (isLoggedIn()) {
            redirect('notes');
        }

        $data = [
            'username' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'username_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['username'] = trim($_POST['username']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            $data['confirm_password'] = trim($_POST['confirm_password']);

            $data['username_err'] = validateUsername($data['username']);

            $data['email_err'] = validateEmail($data['email']);

            $data['password_err'] = validatePassword($data['password']);

            if ($data['password'] !== $data['confirm_password']) {
                $data['confirm_password_err'] = "تکرار رمز عبور با رمز وارد شده مطابقت ندارد. ";
            }

            if (empty($data['username_err'])) {
                if ($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = "این نام کاربری قبلا انتخاب شده است.";
                }
            }

            if (empty($data['email_err'])) {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = "این ایمیل قبلا ثبت شده است. ";
                }
            }

            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                if ($this->userModel->register($data)) {
                    redirect('login');
                } else {
                    echo "خطا در ثبت نام";
                }
            }
        }

        $this->view('auth/register', $data);
    }

    public function createUserSession($user)
    {
        startAppSession();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->username;
        $_SESSION['user_email'] = $user->email;


        redirect('notes');
    }

    public function login()
    {

        $loggedInUser = false;

        if (isLoggedIn()) {
            redirect('notes');
        }

        $data = [
            'username' => '',
            'password' => '',
            'username_err' => '',
            'password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data['username'] = trim($_POST['username']);
            $data['password'] = trim($_POST['password']);

            $remember = isset($_POST['remember']);

            if (empty($data['username'])) {
                $data['username_err'] = "لطفا نام کاربری خود را وارد کنید.";
            }

            $data['password_err'] = validateLoginPassword($data['password']);

            if (empty($data['username_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data);

                if ($loggedInUser) {

                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expires = time() + (86400 * 30);

                        $this->userModel->storeRememberToken(
                            $loggedInUser->id,
                            hash('sha256', $token),
                            date('Y-m-d H:i:s', $expires)
                        );

                        setcookie(
                            'remember_token',
                            $token,
                            rememberCookieOptions($expires)
                        );
                    } else {
                        clearRememberCookie();
                    }

                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = "نام کاربری یا رمز عبور اشتباه است.";
                }
            }
        }

        $this->view('auth/login', $data);
    }

    public function logout()
    {
        $token = $_COOKIE['remember_token'] ?? '';

        if (
            is_string($token) &&
            preg_match('/\A[a-f0-9]{64}\z/i', $token)
        ) {
            $this->userModel->deleteRememberToken(
                hash('sha256', $token)
            );
        }

        clearRememberCookie();

        if (
            session_status() === PHP_SESSION_NONE &&
            isset($_COOKIE[session_name()])
        ) {
            startAppSession();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $sessionCookie = session_get_cookie_params();
                setcookie(session_name(), '', [
                    'expires' => time() - 42000,
                    'path' => $sessionCookie['path'],
                    'domain' => $sessionCookie['domain'],
                    'secure' => $sessionCookie['secure'],
                    'httponly' => $sessionCookie['httponly'],
                    'samesite' => $sessionCookie['samesite'] ?? 'Lax'
                ]);
            }

            session_destroy();
        }

        redirect('login');
    }
}
