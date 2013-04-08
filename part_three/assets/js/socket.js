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

    // on 'broadcastNewPost' update the message list from other users
    updateMessages : function(data) {
      var isNotAdmin = !userIsAnAdmin();
      var isAdminAndBroadcastAdmin =  (userIsAnAdmin() && data.team === 'admin');
      if( ( !userIsAnAdmin() && data.team != 'admin') ||
          ( userIsAnAdmin() && data.team === 'admin') ){
        App.showBroadcastedMessage(data.message);
      }
    },

    sendNewPost : function(msg,team) {
      MY_Socket.socket.emit('newPost',msg,team);
    },

    joinRoom : function(){
      var sessionId = readCookie('ci_session');
      if(sessionId) {
        MY_Socket.socket.emit('joinRoom',sessionId);
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
