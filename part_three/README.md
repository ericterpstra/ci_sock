## Sample CodeIgniter

This is a very simple CRUD-like CodeIgniter project. It is similar to what you might find as a tutorial project on the [CI website][http://ellislab.com/codeigniter/user-guide/tutorial/index.html], or on [Nettuts][http://net.tutsplus.com/sessions/codeigniter-from-scratch/].  

There is a very simple login/auth mechanism that uses the CodeIgniter Session helper.  The CRUD content is a simple message and timestamp.  A user posts a message, and it gets saved to the 'posts' table via CodeIgniter.  If any other users are logged in at the time, they will receive a notification that another user has posted a message via a socket.io connection (not yet implemented). Socket.io connections will only be possible between authenticated users.

### Installation

Check out the files into "ci\_sock" subdirectory of your webroot. Import the cisock.sql database into MySQL.  Edit the hostname, username, and password of your database in /application/config/database.php.  Edit the $config['base\_url'] in /application/config/config.php to match your localhost url (e.g. http://localhost/ci\_sock/).  

The login info for user 1 is:
login: admin@example.com
pass:  password
