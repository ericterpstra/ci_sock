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
    $this->load->model('post_m');
    $this->load->model('user_m');

    // Get some data from the user's session
    $user_id = $this->session->userdata('id');
    $is_admin = $this->session->userdata('isAdmin');
    $team_id = $this->session->userdata('teamId');

    // Load all of the logged-in user's posts
    $posts = $this->post_m->get_posts_for_user( $user_id, 5 );

    // If posts were fetched from the database, assign them to $data
    // so they can be passed into the view.
    if ($posts) {
      $data['posts'] = $posts;
    }

    // Load posts based on the user's permission. Admins can see
    // everything, and regular users can only see posts from
    // their own team.
    $other_users_posts = $this->post_m->get_all_other_posts( $user_id, $team_id, $is_admin );
    if( $other_users_posts ) {
      $data['other_posts'] = $other_users_posts;
    }

    $data['is_admin'] = $is_admin;
    $data['max_posts'] = $posts ? count($posts) : 0;
    $data['post_count'] = $this->post_m->get_post_count_for_user( $user_id );
    $data = $this->user_m->fill_session_data($data);

    $this->load->view('main',$data);
  }

  function post_message() {
    $this->load->model('user_m');
    $message = $this->input->post('message');

    if ( $message ) {
      $this->load->model('post_m');
      $saved = $this->post_m->save_post($message);
    }

    if ( isset($saved) && $saved ) {
      // Gather up data to fill the message template
      $post_data = array();
      $post_data = $this->user_m->fill_session_data($post_data);
      $post_data['body'] = $saved['body'];
      $post_data['createdDate'] = $saved['createdDate'];

      // Create a message html partial from the 'single_post' template and $post_data
      $broadcastMessage = $this->load->view('single_post',$post_data,true);

      // Create an html snipped for the user's message table.
      $myMessage = "<tr><td>". $saved['body'] ."</td><td>". $saved['createdDate'] ."</td></tr>";

      // Create some data to return to the client.
      $output = array('myMessage'=>$myMessage,
                      'broadcastMessage'=>$broadcastMessage,
                      'team'=>$post_data['teamId']);

      // Encode the data into JSON
      $this->output->set_content_type('application/json');
      $output = json_encode($output);

      // Send the data back to the client
      $this->output->set_output($output);
    } else {

    }
  }

  function create_new_user() {
    $userInfo = $this->input->post(null,true);

    if( count($userInfo) ) {
      $this->load->model('user_m');
      $saved = $this->user_m->create_new_user($userInfo);
    }

    if ( isset($saved) && $saved ) {
       echo "success";
    }
  }

  function update_tagline() {
    $new_tagline = $this->input->post('message');
    $user_id = $this->session->userdata('id');

    if( isset($new_tagline) && $new_tagline != "" ) {
      $this->load->model('user_m');
      $saved = $this->user_m->update_tagline($user_id, $new_tagline);
    }

    if ( isset($saved) && $saved ) {
      $this->session->set_userdata(array('tagline'=>$new_tagline));
      echo "success";
    }
  }

}
