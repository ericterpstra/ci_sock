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
    $posts = $this->post->get_posts_for_user( $user_id, 5 );

    if ($posts) {
      $data['posts'] = $posts;
    }

    $data['max_posts'] = $posts ? count($posts) : 0;
    $data['post_count'] = $this->post->get_post_count_for_user( $user_id );
    $data['email'] = $this->session->userdata('email');
    $data['name'] = $this->session->userdata('name');

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

}
