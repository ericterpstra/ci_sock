<?php

class main extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

    if( !$this->session->userdata('isLoggedIn') ) {
        redirect('/login/show_login');
    }
  }

  function show_main() {
    $this->load->model('post');

    $user_id = $this->session->userdata('id');
    $is_admin = $this->session->userdata('isAdmin');
    $posts = $this->post->get_posts_for_user( $user_id, 5 );

    if ($posts) {
      $data['posts'] = $posts;
    }

    $other_users_posts = $this->post->get_all_posts_not_user( $user_id );
    if( $other_users_posts ) {
      $data['other_posts'] = $other_users_posts;
    }

    $data['is_admin'] = $is_admin;
    $data['max_posts'] = $posts ? count($posts) : 0;
    $data['post_count'] = $this->post->get_post_count_for_user( $user_id );
    $data['email'] = $this->session->userdata('email');
    $data['name'] = $this->session->userdata('name');
    $data['avatar'] = $this->session->userdata('avatar');
    $data['tagline'] = $this->session->userdata('tagline');

    $this->load->view('main',$data);
  }

  function post_message() {
    $message = $this->input->post('message');

    if ( $message ) {
      $this->load->model('post');
      $saved = $this->post->save_post($message);
    }

    if ( isset($saved) && $saved ) {
       echo "<tr><td>". $saved['body'] ."</td><td>". $saved['createdDate'] ."</td></tr>";
    } else {

    }
  }

  function create_new_user() {
    $userInfo = $this->input->post(null,true);

    if( count($userInfo) ) {
      $this->load->model('user');
      $saved = $this->user->create_new_user($userInfo);
    }

    if ( isset($saved) && $saved ) {
       echo "success";
    }

  }

}
