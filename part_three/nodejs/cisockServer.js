/**
 * Created with IntelliJ IDEA.
 * User: eric
 * Date: 3/29/13
 * Time: 12:30 PM
 * To change this template use File | Settings | File Templates.
 */

var io = require('socket.io').listen(8080);

io.sockets.on('connection', function (socket) {
  socket.emit('newMessage', { message: 'I Am Working!!' });
  socket.on('newPost', function (post) {
    console.log(post);
    var returnMsg = {message: post};
    socket.broadcast.emit('newMessage',returnMsg);
  });
});