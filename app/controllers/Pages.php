<?php
    
    //Page Controller
    
    class Pages extends Controller{
        
        public function __construct(){

        }

        public function index(){

            if(isLoggedIn){
                redirect('posts');
            }

            $data = [
                'title' => 'Shareposts',
                'description' => 'Share Post is a Single Application that build with MVC design Pattern'
            ];

            $this->view('pages/index', $data);
        }

        public function about(){
            $data = [
                'title' => 'About',
            ];
            
           $this->view('pages/about', $data);
        }
    }