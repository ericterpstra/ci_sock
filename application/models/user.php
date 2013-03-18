<?php


class user extends CI_Model {

    var $details;

    function validate_user( $email, $password ) {
        $this->db->from('user');
        $this->db->where('email',$email );
        $this->db->where( 'password', sha1($password) );
        $login = $this->db->get()->result();

        if ( is_array($login) && count($login) == 1 ) {
            $this->details = $login[0];
            $this->set_session();
            return true;
        }

        return false;
    }

    function set_session() {
        $this->session->set_userdata( array(
                'id'=>$this->details->id,
                'name'=> $this->details->firstName . ' ' . $this->details->lastName,
                'email'=>$this->details->email,
                'avatar'=>$this->details->avatar,
                'tagline'=>$this->details->tagline,
                'isAdmin'=>$this->details->isAdmin,
                'teamId'=>$this->details->teamId,
                'isLoggedIn'=>true
            )
        );
    }

    function  create_new_user( $userData ) {
      $data['firstName'] = $userData['firstName'];
      $data['lastName'] = $userData['lastName'];
      $data['teamId'] = (int) $userData['teamId'];
      $data['isAdmin'] = (int) $userData['isAdmin'];
      $data['avatar'] = $this->getAvatar();
      $data['email'] = $userData['email'];
      $data['tagline'] = "";
      $data['password'] = sha1($userData['password1']);

      return $this->db->insert('user',$data);
    }

    private function getAvatar() {
      $avatar_names = array();

      foreach(glob('assets/img/avatars/*.png') as $avatar_filename){
        $avatar_filename =   str_replace("assets/img/avatars/","",$avatar_filename);
        array_push($avatar_names, str_replace(".png","",$avatar_filename));
      }

      return $avatar_names[array_rand($avatar_names)];
    }
}
