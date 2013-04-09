$(function(){

  window.MY_Socket = {

    // Instantiate the Socket.IO client and connect to the server
    socket : io.connect('http://localhost:8080'),

    // Set up the initial event handlers for the Socket.IO client
    bindEvents : function() {
      this.socket.on('startup',MY_Socket.startupMessage);
      this.socket.on('broadcastNewPost',MY_Socket.updateMessages);
    },

    // This just indicates that a Socket.IO connection has begun.
    startupMessage : function(data) {
      console.log(data.message);
    },

    // on 'broadcastNewPost' update the message list from other users
    updateMessages : function(data) {
      // Because the message is broadcasted twice (once for the team, again for the admins)
      // we need to make sure it is only displayed once if the Admin is also on the same
      // team as the sender.
      if( ( !userIsAnAdmin() && data.team != 'admin') ||
          ( userIsAnAdmin() && data.team === 'admin') ){
        // Send the html partial with the new message over to the jQuery function that will display it.
        App.showBroadcastedMessage(data.message);
      }
    },

    // This will emit an html partial containing a new message,
    // plus the teamId of the user sending the message.
    sendNewPost : function(msg,team) {
      MY_Socket.socket.emit('newPost',msg,team);
    },

    // Join a socket.io 'room' based on the user's team
    joinRoom : function(){
      // get the CodeIgniter sessionID from the cookie
      var sessionId = readCookie('ci_session');

      if(sessionId) {
        // Send the sessionID to the Node server in an effort to join a 'room'
        MY_Socket.socket.emit('joinRoom',sessionId);
      } else {
        // If no sessionID exists, don't try to join a room.
        console.log('No session id found. Broadcast disabled.');
        //forward to logout url?
      }
    }

  } // end window.MY_Socket

  // Start it up!
  MY_Socket.bindEvents();

  // Read a cookie. http://www.quirksmode.org/js/cookies.html#script
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

  /* This will look for the 'Admin' badge on the current window.
     This is a super-hacky method of determining if the user is an admin
     So that messages from user's on the same team as the admin won't get
     doubled up in the message stream. */
  function userIsAnAdmin(){
    var val = false;
    $('.userTeamBadge').children().each(function(i,el){
       if ($(el).text() == 'Admin'){
         val = true;
       }
    });
    return val;
  }
});
