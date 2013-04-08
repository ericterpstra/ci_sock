
var io = require('socket.io').listen(8080,{
  'log level':1
});
var redis = require('redis');

io.sockets.on('connection', function (socket) {
  var rClient = redis.createClient();

  // Let everyone know it's working
  socket.emit('startup', { message: 'I Am Working!!' });

  socket.on('newPost', function (post,team,sessionId) {
    //console.log(post);
    console.log('Broadcasting a post to team: ' + team.toString());

    var broadcastData = {message: post, team: team};
    socket.broadcast.to(team.toString()).emit('broadcastNewPost',broadcastData);
    broadcastData.team = 'admin';
    socket.broadcast.to('admin').emit('broadcastNewPost',broadcastData);
  });

  socket.on('joinRoom', function(sessionId){
    var parsedRes, team, isAdmin;

    rClient.get('sessions:'+sessionId, function(err,res){
      console.log(res);
      parsedRes = JSON.parse(res);
      team = parsedRes.teamId;
      isAdmin = parsedRes.isAdmin;

      console.log('Joining room ' + team.toString());
      socket.join(team.toString());

      if (isAdmin) {
        console.log('Joining room for Admins');
        socket.join('admin');
      }
    });

  });
});
