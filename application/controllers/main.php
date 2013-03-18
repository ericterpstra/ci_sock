<?php

class main extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

    if( !$this->session->userdata('isLoggedIn') ) {
        redirect('/login/show_login');
    }
  }

  /**
   * This is the controller method that drives the application.
   * After a user logs in, show_main() is called and the main
   * application screen is set up.
   */
  function show_main() {
    $this->load->model('post');

    // Get some data from the user's session
    $user_id = $this->session->userdata('id');
    $is_admin = $this->session->userdata('isAdmin');
    $team_id = $this->session->userdata('teamId');

    // Load all of the logged-in user's posts
    $posts = $this->post->get_posts_for_user( $user_id, 5 );

    // If posts were fetched from the database, assign them to $data
    // so they can be passed into the view.
    if ($posts) {
      $data['posts'] = $posts;
    }

    // Load posts based on the user's permission. Admins can see
    // everything, and regular users can only see posts from
    // their own team.
    $other_users_posts = $this->post->get_all_other_posts( $user_id, $team_id, $is_admin );
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
