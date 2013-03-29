<?php //https://gist.github.com/zhouming/3672207 ?>
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session{

  private $sess_use_redis = TRUE;
  private $redis = '';

  public function __construct($params = array()) {
    //parent::__construct();
    $this->CI =& get_instance();

    foreach (array('sess_encrypt_cookie', 'sess_use_database', 'sess_table_name', 'sess_expiration', 'sess_expire_on_close', 'sess_match_ip', 'sess_match_useragent', 'sess_cookie_name', 'cookie_path', 'cookie_domain', 'cookie_secure', 'sess_time_to_update', 'time_reference', 'cookie_prefix', 'encryption_key') as $key)
    {
      $this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
    }

    if ($this->encryption_key == '')
    {
      show_error('In order to use the Session class you are required to set an encryption key in your config file.');
    }

    // Load the string helper so we can use the strip_slashes() function
    $this->CI->load->helper('string');

    // Do we need encryption? If so, load the encryption class
    if ($this->sess_encrypt_cookie == TRUE)
    {
      $this->CI->load->library('encrypt');
    }

    if ($this->sess_use_redis == TRUE) {
      $this->redis = new Redis();
      $this->redis->connect($this->CI->config->item('redis_host'), $this->CI->config->item('redis_port'));
      //$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP); //php5.4 can not work
      $this->redis->setOption(Redis::OPT_PREFIX, 'sessions:');
    }

    // Set the "now" time.  Can either be GMT or server time, based on the
    // config prefs.  We use this to set the "last activity" time
    $this->now = $this->_get_time();

    // Set the session length. If the session expiration is
    // set to zero we'll set the expiration two years from now.
    if ($this->sess_expiration == 0)
    {
      $this->sess_expiration = (60*60*24*365*2);
    }

    // Set the cookie name
    $this->sess_cookie_name = $this->cookie_prefix.$this->sess_cookie_name;

    // Run the Session routine. If a session doesn't exist we'll
    // create a new one.  If it does, we'll update it.
    if ( ! $this->sess_read()) {
      $this->sess_create();
    } else {
      $this->sess_update();
    }

    // Delete 'old' flashdata (from last request)
    $this->_flashdata_sweep();

    // Mark all new flashdata as old (data will be deleted before next request)
    $this->_flashdata_mark();

    // Delete expired sessions if necessary
    $this->_sess_gc();

    log_message('debug', "Session routines successfully run");
  }

  function sess_read() {

    $session = array();
    // Fetch the cookie
    $session_id = $this->CI->input->cookie($this->sess_cookie_name);
    // No cookie?  Goodbye cruel world!...
    if ($session_id === FALSE) {
      log_message('debug', 'A session cookie was not found.');
      return FALSE;
    }

    $session['session_id'] = $session_id;
    $session['ip_address'] = $this->CI->input->ip_address();
    $session['user_agent'] = trim(substr($this->CI->input->user_agent(), 0, 120));

    // Is there a corresponding session in the DB?
    if ($this->sess_use_redis=== TRUE) {
      //$this->CI->db->where('session_id', $session['session_id']);

      $row = json_decode($this->redis->get($session['session_id']), TRUE);

      if ( ! $row) {
        $this->sess_destroy();
        return FALSE;
      }

      if ($this->sess_match_ip == TRUE AND $row['ip_address']  != $session['ip_address']) {
        $this->sess_destroy();
        return FALSE;
      }

      if ($this->sess_match_useragent == TRUE AND $row['user_agent'] != $session['user_agent']) {
        $this->sess_destroy();
        return FALSE;
      }

      // Is there custom data?  If so, add it to the main session array
      $session['last_activity'] = $row['last_activity'];
      if (isset($row['user_data']) AND $row['user_data'] != '') {
        $custom_data = $this->_unserialize($row['user_data']);

        if (is_array($custom_data)) {
          foreach ($custom_data as $key => $val) {
            $session[$key] = $val;
          }
        }
      }
    }

    // Session is valid!
    $this->userdata = $session;
    unset($session);

    return TRUE;
  }

  function sess_create() {
    $sessid = '';
    while (strlen($sessid) < 32) {
      $sessid .= mt_rand(0, mt_getrandmax());
    }

    // To make the session ID even more secure we'll combine it with the user's IP
    $sessid .= $this->CI->input->ip_address();

    $this->userdata = array(
      'session_id'	=> md5(uniqid($sessid, TRUE)),
      'ip_address'	=> $this->CI->input->ip_address(),
      'user_agent'	=> substr($this->CI->input->user_agent(), 0, 120),
      'last_activity'	=> $this->now,
      'user_data'		=> ''

    );


    // Save the data to the DB if needed
    if ($this->sess_use_redis === TRUE) {
      $has_session = $this->redis->get($this->userdata['session_id']);
      if ($has_session) {
        $this->sess_create();
        return FALSE;
      }
      $this->redis->setex($this->userdata['session_id'], $this->sess_expiration, json_encode($this->userdata));
    }

    // Write the cookie
    $this->_set_cookie();
  }

  function sess_update() {

    // We only update the session every five minutes by default
    if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now) {
      return;
    }

    // Save the old session id so we know which record to
    // update in the database if we need it
    $old_sessid = $this->userdata['session_id'];
    $new_sessid = '';
    while (strlen($new_sessid) < 32) {
      $new_sessid .= mt_rand(0, mt_getrandmax());
    }

    // To make the session ID even more secure we'll combine it with the user's IP
    $new_sessid .= $this->CI->input->ip_address();

    // Turn it into a hash
    $new_sessid = md5(uniqid($new_sessid, TRUE));

    // Update the session data in the session data array
    $this->userdata['session_id'] = $new_sessid;
    $this->userdata['last_activity'] = $this->now;

    // _set_cookie() will handle this for us if we aren't using database sessions
    // by pushing all userdata to the cookie.

    // Update the session ID and last_activity field in the DB if needed
    // set cookie explicitly to only have our session data
    $cookie_data = array();
    $custom_userdata = $this->userdata;
    foreach (array('session_id','ip_address','user_agent','last_activity') as $val) {
      unset($custom_userdata[$val]);
      $cookie_data[$val] = $this->userdata[$val];
    }

    if (count($custom_userdata) === 0) {

      $custom_userdata = '';
    } else {
      if (isset($custom_userdata['user_data'])) {
        unset($custom_userdata['user_data']);
      }
      $custom_userdata = $this->_serialize($custom_userdata);
    }

    if ($this->sess_use_redis === TRUE) {
      $has_session = $this->redis->get($new_sessid);
      if ($has_session) {
        $this->sess_update();
        return FALSE;
      }
      $this->userdata['user_data'] = $custom_userdata;
      $this->redis->setex($new_sessid, $this->sess_expiration, json_encode($this->userdata));
      $this->redis->delete($old_sessid);
    }


    // Write the cookie
    $this->_set_cookie();
  }

  function sess_write() {
    // Are we saving custom data to the DB?  If not, all we do is update the cookie
    if ($this->sess_use_redis === FALSE) {
      $this->_set_cookie();
      return;
    }
    // set the custom userdata, the session data we will set in a second
    $custom_userdata = $this->userdata;
    $cookie_userdata = array();

    // Before continuing, we need to determine if there is any custom data to deal with.
    // Let's determine this by removing the default indexes to see if there's anything left in the array
    // and set the session data while we're at it
    foreach (array('session_id','ip_address','user_agent','last_activity') as $val) {
      unset($custom_userdata[$val]);
      $cookie_userdata[$val] = $this->userdata[$val];
    }

    // Did we find any custom data?  If not, we turn the empty array into a string
    // since there's no reason to serialize and store an empty array in the DB

    if (count($custom_userdata) === 0) {
      $custom_userdata = '';
    } else {
      // Serialize the custom data array so we can store it
      if (isset($custom_userdata['user_data'])) {
        unset($custom_userdata['user_data']);
      }
      $custom_userdata = $this->_serialize($custom_userdata);
    }

    // Run the update query

    $this->userdata['user_data'] = $custom_userdata;
    $this->redis->multi()
      ->delete($this->userdata['session_id'])
      ->setex($this->userdata['session_id'], $this->sess_expiration, json_encode($this->userdata))
      ->exec();

    // Write the cookie.  Notice that we manually pass the cookie data array to the
    // _set_cookie() function. Normally that function will store $this->userdata, but
    // in this case that array contains custom data, which we do not want in the cookie.
    $this->_set_cookie();
  }

  function _set_cookie($cookie_data = NULL) {

    $expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

    // Set the cookie
    setcookie(
      $this->sess_cookie_name,
      $this->userdata['session_id'],
      $expire,
      $this->cookie_path,
      $this->cookie_domain,
      $this->cookie_secure
    );
  }

  function _sess_gc() {
    if ($this->sess_use_redis == TRUE) {
      return;
    }

  }

  function sess_destroy() {
    // Kill the session DB row
    if ($this->sess_use_redis === TRUE AND isset($this->userdata['session_id'])) {

      $this->redis->delete($this->userdata['session_id']);
    }

    // Kill the cookie
    setcookie(
      $this->sess_cookie_name,
      addslashes(serialize(array())),
      ($this->now - 31500000),
      $this->cookie_path,
      $this->cookie_domain,
      0
    );
  }
}
// END MY_Session Class

/* End of file MY_Session.php */
/* Location: ./libraries/MY_Session.php */