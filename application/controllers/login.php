<?php

class login extends CI_Controller {

    function index() {
        if( $this->session->userdata('isLoggedIn') ) {
            redirect('/main/show_main');
        } else {
            $this->show_login(false);
        }
    }

    function login_user() {
        $this->load->model('user');
        $email = $this->input->post('email');

        $pass  = $this->input->post('password');
        $login = false;

        if( $email && $pass && $this->user->validate_user($email,$pass)) {
            redirect('/main/show_main');
        } else {
            $this->show_login(true);
        }
    }

    function show_login( $show_error = false ) {
        $data['error'] = $show_error;

        $this->load->helper('form');
        $this->load->view('login',$data);
    }

    function logout_user() {
      $this->session->sess_destroy();
      $this->index();
    }

    function showphpinfo() {
        echo phpinfo();
    }


}
