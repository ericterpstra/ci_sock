
var io = require('socket.io').listen(8080);
var redis = require('redis');

io.sockets.on('connection', function (socket) {
  var rClient = redis.createClient();

  // Let everyone know it's working
  socket.emit('startup', { message: 'I Am Working!!' });

  socket.on('newPost', function (post,team) {
    //console.log(post);
    console.log('Broadcasting a post to team: ' + team.toString());

    rClient.keys('*',function (err, keys) {
      if (err) return console.log(err);

      for(var i = 0, len = keys.length; i < len; i++) {
          console.log(keys[i]);
      }
    });

    var broadcastData = {message: post, team: team};
    socket.broadcast.to(team.toString()).emit('broadcastNewPost',broadcastData);
    broadcastData.team = 'admin';
    socket.broadcast.to('admin').emit('broadcastNewPost',broadcastData);
  });

  socket.on('joinRoom', function(team,isAdmin){
    console.log('Joining room ' + team.toString());
    socket.join(team.toString());


    if (isAdmin) {
      console.log('Joining room for Admins');
      socket.join('admin');
    }

  });
});
