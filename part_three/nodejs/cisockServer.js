// Start up a Node server with Socket.IO
var io = require('socket.io').listen(8080,{
  'log level':1
});

// Let Node know that you want to use Redis
var redis = require('redis');

// Listen for the client connection event
io.sockets.on('connection', function (socket) {
  // Instantiate a Redis client that can issue Redis commands.
  var rClient = redis.createClient();

  // Let everyone know it's working
  socket.emit('startup', { message: 'I Am Working!!' });

  socket.on('newPost', function (post,team,sessionId) {
    console.log('Broadcasting a post to team: ' + team.toString());

    // Broadcast the message to the sender's team
    var broadcastData = {message: post, team: team};
    socket.broadcast.to(team.toString()).emit('broadcastNewPost',broadcastData);

    // Broadcast the message to all admins
    broadcastData.team = 'admin';
    socket.broadcast.to('admin').emit('broadcastNewPost',broadcastData);
  });

  // Handle a request to join a room from the client
  // sessionId should match the Session ID assigned by CodeIgniter
  socket.on('joinRoom', function(sessionId){
    var parsedRes, team, isAdmin;

    // Use the redis client to get all session data for the user
    rClient.get('sessions:'+sessionId, function(err,res){
      console.log(res);
      parsedRes = JSON.parse(res);
      team = parsedRes.teamId;
      isAdmin = parsedRes.isAdmin;

      // Join a room that matches the user's teamId
      console.log('Joining room ' + team.toString());
      socket.join(team.toString());

      // Join the 'admin' room if user is an admin
      if (isAdmin) {
        console.log('Joining room for Admins');
        socket.join('admin');
      }
    });

  });
});
