#CodeIgniter + Socket.IO (with Redis) Sample Project


## Part One
The part\_one folder contains a plain old PHP/MySQL project utilizing the CodeIgniter framework. Styling is provided by Twitter Bootstrap.  The application uses the default Session plugin provided by CodeIgniter.

Read More: [A Sample CodeIgniter Application with Login and Session](http://ericterpstra.com/2013/03/a-sample-codeigniter-application-with-login-and-session/ "Part One Blog Link")
 
## Part Two
These files allow the use of Redis as a storage medium for CodeIgniter's session data instead of a MySQL table.  The MY\_Session class overrides several methods in CodeIgniter's Session class so that persistent session data is stored and retrieved from Redis.

Read More: [Use Redis instead of MySQL for CodeIgniter Session Data](http://ericterpstra.com/2013/03/use-redis-instead-of-mysql-for-codeigniter-session-data/)

## Part Three
A refactored version of part one.  It includes the changes from part 2, but also features real-time updates using Socket.IO and a NodeJS server.  Data is shared between NodeJS and PHP via Redis.

The user interface is unchanged, but when a message is posted by a user, any other user with the same team ID will instantly recieve the message and have it posted in the team messages list.  Admins will recieve messages from all users, regardless of team ID.  

Read More: [Live Updates in CodeIgniter with Socket.IO and Redis](http://ericterpstra.com/2013/04/live-updates-in-codeigniter-with-socket-io-and-redis/)
