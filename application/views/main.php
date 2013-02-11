<?php $this->load->view('header') ?>

  <div class="container">

    <!-- User Info -->
    <div class="row">
      <div class="span4 offset4 well">
        <div class="row">
          <div class="span1"><a href="http://critterapp.pagodabox.com/others/admin" class="thumbnail"><img
                src="http://critterapp.pagodabox.com/img/user.jpg" alt=""></a></div>
          <div class="span3">
            <p><strong> <?php echo $name ?> </strong></p>
            <span class=" badge badge-warning">
              <?php echo $post_count ?> messages
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Message Box -->
    <div class="row">
      <div class="span4 offset4 well">
        <textarea class="span4" id="txtNewMessage" name="txtNewMessage"
                  placeholder="Type in your message" rows="5"></textarea>
        <h6 class="pull-right"><span id="spanNumChars">320</span> characters remaining</h6>
        <button id="btnPost" class="btn btn-info">Post New Message</button>
      </div>
    </div>

    <!-- List of Current User Posts -->
    <div class="row">

      <?php if ( $max_posts ) : ?>
        <div class="span4 offset4">
          <h4>Last <?php echo count($posts) ?> Messages:</h4>
        </div>
        <div class="span4 offset4 well">
          <table id="tblMyMessages" class="table table-condensed table-hover">
            <thead>
            <tr>
              <th class="span2">Message</th>
              <th class="span1">Sent</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach( $posts as $post ) : ?>
            <tr>
              <td><?php echo $post['body'] ?></td>
              <td><?php echo $post['createdDate'] ?></td>
            </tr>
            <?php endforeach; ?>

            </tbody>
          </table>
        </div>
      <?php else : ?>
        <div class="span4 offset4">
          <h4>You have posted no messages.</h4>
        </div>
      <?php endif; ?>

    </div>

  </div>

<?php $this->load->view('footer') ?>