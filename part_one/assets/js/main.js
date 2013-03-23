$(function () {

  var App = {

    //TODO: Find a better way to set these from config.php
    baseUrl : '/ci_sock/part_one/',
    maxCharacters: 320,
    maxPostsPerPage : 5,

    init: function () {
      this.setElements();
      this.bindEvents();
      this.setupComponents();
    },

    // Cache all the jQuery selectors for easy reference.
    setElements: function () {
      this.$messageBox = $('#txtNewMessage');
      this.$numChars = $('#spanNumChars');
      this.$postButton = $('#btnPost');
      this.$myMessages = $('#tblMyMessages tbody');
      this.$newUserButton = $('#btnModalSubmit');
      this.$modalWindow = $('#myModal');
      this.$otherPostAvatars = $('.otherAvatar img');
      this.$tagline = $('#pTagline');
      this.$taglineText = this.$tagline.html();
      this.$totalMessageCount = $('.totalMessageCount');
      this.$messageCount = $('.messageCount');
    },

    // Bind document events and assign event handlers.
    bindEvents: function () {
      this.$messageBox.on('input propertychange', this.updateNumChars);
      this.$postButton.on('click', this.postMessage);
      this.$newUserButton.on('click', this.addNewUser);
      this.$tagline.on('blur',this.saveTagline);
    },

    // Initialize any extra UI components
    setupComponents : function () {
      // Set up the popovers when hovering over another user's avatar.
      this.$otherPostAvatars.popover({
        html:true,
        placement:'left',
        trigger: 'hover'
      });
    },

    /* *************************************
     *             Event Handlers
     * ************************************* */

    /**
     * Click handler for the Create button in
     * the New User modal window. It grabs data
     * from the form and submits it to the
     * create_new_user function in the Main controller.
     *
     * @param e event
     */
    addNewUser : function (e) {
      var formData = {
        firstName : $('#first_name').val(),
        lastName  : $('#last_name').val(),
        email     : $('#email').val(),
        isAdmin   : $('#isAdmin').is(':checked'),
        teamId    : $('#teamId').val(),
        password1 : $('#password').val(),
        password2 : $('#password2').val()
      };
      // TODO: Client-side validation goes here

      var postUrl = App.baseUrl + '/index.php/main/create_new_user';

      $.ajax({
        type: 'POST',
        url: postUrl,
        dataType: 'text',
        data: formData,
        success: App.newUserCreated,
        error: App.alertError
      })

    },

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
        });
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
     * Update the user's number of messages label after adding
     * a new message.
     */
    updateMessageCount : function() {
      var tMessages = parseInt( App.$totalMessageCount.text() );
      var messages = parseInt( App.$messageCount.text() );

      App.$totalMessageCount.text( tMessages + 1 );

      // If the messages list has less than 5 messages, update the count label
      if ( messages >= 0 && messages < App.maxPostsPerPage ) {
        App.$messageCount.text( messages + 1 );
      }
    },

    /**
     * The user clicked the tagline area and changed some text.
     * This will save the changed text to the server and update the
     * cached tagline.
     *
     * @param e
     */
    saveTagline : function(e) {
      var newText = $(this).html();
      if( App.$taglineText !== newText ) {
        var postUrl = App.baseUrl + '/index.php/main/update_tagline';
        $.ajax({
          type: "POST",
          url: postUrl,
          data: {message : newText},
          success: function(res){App.$taglineText=newText;},
          error: App.alertError,
          dataType: 'html'
        });
      }
    },

    /* *************************************
     *             AJAX Callbacks
     * ************************************* */


     /**
     * Get the newly posted message back from the server
     * and prepend it to the message list.
     *
     * @param result An HTML <tr> string with the new message
     */
    successfulPost : function( result ) {
      var messageRows = App.$myMessages.children();

      // Reset text box
      App.$messageBox.val('');
      App.$numChars.text(App.maxCharacters);

      // Remove the last posted message from the list
      if ( messageRows.length >= App.maxPostsPerPage ) {
        messageRows.last().remove();
      } else if (messageRows.length <= 1) {
        window.location.reload(true);
      }

      App.updateMessageCount();

      // Put the newly posted message at the top
      App.$myMessages.prepend( result );

      // Send socket.io notification
    },

    /**
     * A new user has been created, and the server has responded (or errored)
     * @param response
     */
    newUserCreated : function(response) {
      if ( response ) {
        App.$modalWindow.modal('hide');
      }
      // TODO: if response not true, show server validation errors
    },

    /**
     * Util method for blasting an error message on the screen.
     * @param error
     */
    alertError : function( error ) {
       var args = arguments;
       var msg = error.responseText;
    }

  };

  App.init();

});
