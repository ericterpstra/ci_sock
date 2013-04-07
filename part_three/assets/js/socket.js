$(function(){

  window.MY_Socket = {

    socket : io.connect('http://localhost:8080'),

    bindEvents : function() {
      this.socket.on('startup',MY_Socket.startupMessage);
      this.socket.on('broadcastNewPost',MY_Socket.updateMessages);
    },

    startupMessage : function(data) {
      console.log(data.message);
    },

    updateMessages : function(data) {
      if(data.team && data.team != 'admin'){
        App.showBroadcastedMessage(data.message);
      }
    },

    sendNewPost : function(msg,team) {
      MY_Socket.socket.emit('newPost',msg,team);
    },

    joinRoom : function(){
      var ciCookie = readCookie('ci_session');
      if(ciCookie) {
        MY_Socket.socket.emit('joinRoom',ciCookie);
      } else {
        console.log('No session id found. Broadcast disabled.');
        //forward to logout url?
      }
    }

  } // end MY_Socket

  MY_Socket.bindEvents();

  // http://www.quirksmode.org/js/cookies.html#script
  function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
  }
});
