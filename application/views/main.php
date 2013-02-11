<?php $this->load->view('header') ?>

  <div class="navbar">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="#" name="top">Post Message App</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li><a href="#"><i class="icon-home"></i> Home</a></li>
            <li class="divider-vertical"></li>
          </ul>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-wrench"></i> admin	<span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a data-toggle="modal" href="#myModal"><i class="icon-user"></i> New User</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo base_url() ?>/index.php/login/logout_user"><i class="icon-share"></i> Logout</a></li>
            </ul>
          </div>
        </div>
        <!--/.nav-collapse -->
      </div>
      <!--/.container-fluid -->
    </div>
    <!--/.navbar-inner -->
  </div>
  <!--/.navbar -->

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


  <!-- ****************************************************************** -->
  <!--                        NEW USER Modal Window                       -->
  <!-- ****************************************************************** -->

  <div class="modal hide" id="myModal">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">x</button>
      <h3>New User Details</h3>
    </div>
    <div class="modal-body">
        <p><input type="text" class="span4" name="first_name" id="first_name" placeholder="First Name"></p>
        <p><input type="text" class="span4" name="last_name" id="last_name" placeholder="Last Name"></p>
        <p><input type="text" class="span4" name="email" id="email" placeholder="Email"></p>
        <p><input type="password" class="span4" name="password" id="password" placeholder="Password"></p>
        <p><input type="password" class="span4" name="password2" id="password2" placeholder="Confirm Password"></p>
    </div>
    <div class="modal-footer">
      <a href="#" class="btn btn-warning" data-dismiss="modal">Cancel</a>
      <a href="#" id="btnModalSubmit" class="btn btn-primary">Create</a>
    </div>
  </div>
<?php $this->load->view('footer') ?>