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
      if(!(App.isAdmin && data.team == App.team.toString())){
        App.showBroadcastedMessage(data.message);
      }
    },

    sendNewPost : function(msg,team) {
      MY_Socket.socket.emit('newPost',msg,team);
    },

    joinRoom : function(team, isAdmin){
      MY_Socket.socket.emit('joinRoom',team,isAdmin);
    }

  } // end MY_Socket

  MY_Socket.bindEvents();

});
