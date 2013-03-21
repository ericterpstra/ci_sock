$(function () {

  var App = {

    //TODO: Find a better way to set these from config.php
    baseUrl : '/ci_sock',
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

    // Update the displayed user message count
    updateMessageCount : function(numMsgs) {
      if(numMsgs) {
        $('.messageCount').each(function(index, el) {
            $(el).html(parseInt($(el).text()) + 1);
          });
      }
    },

    // Update the tagline if it changed
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
      } else if ( messageRows.length > 0 && messageRows.length < App.maxPostsPerPage ){
        App.updateMessageCount( messageRows.length );
      } else {
        window.location.reload(true);
      }

      // Put the newly posted message at the top
      App.$myMessages.prepend( result );

      // Send socket.io notification
    },

    newUserCreated : function(response) {
      if ( response ) {
        App.$modalWindow.modal('hide');
      }
      // TODO: if response not true, show server validation errors
    },

    alertError : function( error ) {
       var args = arguments;
       var msg = error.responseText;
    }

  };

  App.init();

});