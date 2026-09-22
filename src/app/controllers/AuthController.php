<?php 

class AuthController extends Controller{
 
    private $userModel;

    public function __construct(){

        $this->userModel = new User();

    }

    public function register(){
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data['username'] = trim($_POST['username']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);

            $data['username_err'] = validateUsername($data['username']);

            $data['email_err'] = validateEmail($data['email']);

            $data['password_err'] = validatePassword($data['password']);

            if($data['password'] !== $data['confirm_password']){
                $data['confirm_password_err'] = "تکرار رمز عبور با رمز وارد شده مطابقت ندارد. ";
            }

            if(empty($data['username_err'])){
                if($this->userModel->findUserByUsername($data['username'])){
                    $data['username_err'] = "این نام کاربری قبلا انتخاب شده است.";
                }
            }

            if(empty($data['email_err'])){
                if($this->userModel->findUserByEmail($data['email'])){
                    $data['email_err'] = "این ایمیل قبلا ثبت شده است. ";
                }
            }

            if(empty($data['username_err'])&& empty($data['email_err'])&& empty($data['password_err'])&& empty($data['confirm_password_err'])){
                if($this->userModel->register($data)){
                    echo "با موفقیت ثبت نام شدید.";
                }else{
                    echo"خطا در ثبت نام";
                }
            }

        }   

        $this->view('auth/register' , $data);

    }

    public function login(){
        $data = [
            'username' => '',
            'password' => '',
            'username_err' => '',
            'password_err' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data['username'] = trim($_POST['username']);
            $data['password'] = trim($_POST['password']);

            $data['username_err'] = validateUsername($data['username']);
            $data['password_err'] = validatePassword($data['password']);
        }

        $this->view('auth/login', $data);
        
    }

}