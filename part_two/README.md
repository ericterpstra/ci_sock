The files in this folder will enable the use of Redis to store CodeIgniter session data instead of a MySQL table.

config.php contains two extra lines:
    $config['redis_host'] = 'localhost';
    $config['redis_port'] = '6379';

Change 'localhost' and '6379' to your Redis location and port, respectively.

Place MY_Session.php into the 'application/libraries' directory.  It does not need any modification. CodeIgniter should use it by default as long as the following lines appear in your config.php:

    $config['subclass_prefix'] = 'MY_';
    $config['sess_use_database']	= TRUE;

You *must* have the phpredis extension installed and enabled before using any of this.  Get the code for the extension here: https://github.com/nicolasff/phpredis