<?php
    class Users extends Controller {
        public function __construct(){
            $this->userModel = $this->model('User');
        }
 
        public function register(){
            //check for POST
            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                //sanitize POST Data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //Process the form
                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'name_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err'  => '',
                ]; 
                // Validate Email
                if(empty($data['email'])){
                    $data['email_err'] = 'Pleae enter email';
                  } else {
                    // Check email
                    if($this->userModel->findUserByEmail($data['email'])){
                      $data['email_err'] = 'Email is already taken';
                    }
                  }

                // Validate Name
                if(empty($data['name'])){
                    $data['name_err'] = 'please enter name';
                }

                // Validate Password
                if(empty($data['password'])){
                    $data['password_err'] = 'please enter password';
                }elseif(strlen($data['password']) < 6 ){
                    $data['password_err'] = 'Pasword Must Be At Least 6 character';
                }

                // Validate Confirm Password
                if(empty($data['confirm_password'])){
                    $data['confirm_password_err'] = 'please confirm password';
                }else{
                    if($data['password'] != $data['confirm_password']){
                        $data['confirm_password_err'] = 'password do not match';
                    }
                }

                //make sure all err ar empty

                if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
                    //Hash Password
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                    //Register User

                    if($this->userModel->register($data)){
                        flash('register_success', 'You are Register and can Log in');
                        redirect('users/login');
                    }else{
                        die('something went wrong');
                    }

                } else{
                    //load view with erros
                    $this->view('users/register', $data);
                }

                
                
            } else {
                //init data
                $data = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'confirm_password' => '',
                    'name_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err'  => '',
                ];   
                //load the view
                $this->view('users/register', $data);
            }
        }

        public function login(){
            //check for POST
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                //sanitize POST Data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //Process the form
                $data = [         
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'email_err' => '',
                    'password_err' => '',
                ]; 
                
                // Validate Email
                if(empty($data['email'])){
                    $data['email_err'] = 'please enter email';
                }

                // Validate Password
                if(empty($data['password'])){
                    $data['password_err'] = 'please enter password';
                }

                //check for user/email

                if($this->userModel->findUserByEmail($data['email'])){
                    //user found
                } else {
                    //user not found
                    $data['email_err'] = 'No User Found';
                }

                //make sure error are empty
                if(empty($data['email_err']) && empty($data['password_err'])){
                    //validate
                    //check and set log in user 
                    $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                    if($loggedInUser){
                        //create session
                        $this->createUserSession($loggedInUser);
                    }else {

                        $data['password_err'] = 'password incorrect';

                        $this->view('users/login', $data);
                    }


                } else{
                    //load view with erros
                    $this->view('users/login', $data);
                }

            } else {
                //init data
                $data = [
                    'email' => '',
                    'password' => '',
                    'email_err' => '',
                    'password_err' => '',
                ];
                //load the view
                $this->view('users/login', $data);
            }
        }

        public function createUserSession($user){
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_name'] = $user->name;
            redirect('posts');
        }

        public function logout(){
            unset($_SESSION['user_id']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_name']);
            session_destroy();
            redirect('users/login');
        }
    }