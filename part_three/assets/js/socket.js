$(function(){

  window.MY_Socket = {

    //socket : io.connect('http://localhost:8080'),

    bindEvents : function() {
      this.socket.on('newMessage',MY_Socket.updateMessages);
    },

    updateMessages : function(data) {
      console.log(data.message);
    },

    sendMessage : function(msg) {
      MY_Socket.socket.emit('newPost',msg);
    }

  } // end MY_Socket

 // MY_Socket.bindEvents();

});
