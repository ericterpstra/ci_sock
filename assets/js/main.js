$(function () {

  var App = {

    baseUrl : '/ci_sock',
    maxCharacters: 320,

    init: function () {
      this.setElements();
      this.bindEvents();

    },

    setElements: function () {
      this.$messageBox = $('#txtNewMessage');
      this.$numChars = $('#spanNumChars');
      this.$postButton = $('#btnPost');
      this.$myMessages = $('#tblMyMessages tbody');
    },

    bindEvents: function () {
      this.$messageBox.on('input propertychange', this.updateNumChars);
      this.$postButton.on('click', this.postMessage);
    },

    /* *************************************
     *             Event Handlers
     * ************************************* */

    /**
     * Handler for 'Post New Message' button click.
     * Sends POST data to the post_message method
     * of the main controller
     *
     * @param e event
     */
    postMessage: function (e) {
      var messageText = App.$messageBox.val();
      var postUrl = App.baseUrl + '/index.php/main/post_message';

      if (messageText.length) {
        $.ajax({
          type: "POST",
          url: postUrl,
          data: {message : messageText},
          success: App.successfulPost,
          error: App.alertError,
          dataType: 'html'
        })
      }
    },

    /**
     * Handler for typing into message textarea.
     * Reduces the characters remaining count by one
     * each time the textarea changes.
     *
     * @param e event
     */
    updateNumChars: function (e) {
      var msgLen = App.$messageBox.val().length;
      var charsLeft = App.maxCharacters - msgLen;

      App.$numChars.text(charsLeft);
    },

    /**
     * Get the newly posted message back from the server
     * and prepend it to the message list.
     *
     * @param result An HTML <tr> string with the new message
     */
    successfulPost : function( result ) {
      // Reset text box
      App.$messageBox.val('');
      App.$numChars.text(App.maxCharacters);

      // Remove the last posted message from the list
      App.$myMessages.children().last().remove();

      // Put the newly posted message at the top
      App.$myMessages.prepend( result );

      // Send socket.io notification
    },

    alertError : function( error ) {
       var args = arguments;
       var msg = error.responseText;
    }

  };

  App.init();

});