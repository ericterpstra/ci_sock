<?php

class post extends CI_Model {

  function save_post( $body ) {

    $data['body'] = $body;
    $data['userId'] = $this->session->userdata('id');
    $data['createdDate'] = date('Y-m-d H:i:s',time());

    if ( $this->db->insert('post',$data) ) {
      return $data;
    } else {
      return false;
    }
  }

  function get_posts_for_user( $user_id, $num_posts = 10 ) {

    $this->db->from('post');
    $this->db->where( array('userId'=>$user_id) );
    $this->db->limit( $num_posts );
    $this->db->order_by('createdDate','desc');

    $posts = $this->db->get()->result_array();

    if( is_array($posts) && count($posts) > 0 ) {
      return $posts;
    }

    return false;
  }


  function get_post_count_for_user( $user_id ) {

    $this->db->from('post');
    $this->db->where( array('userId'=>$user_id) );

    return $this->db->count_all_results();

  }
}
