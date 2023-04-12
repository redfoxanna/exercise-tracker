<?php
session_start();
require_once('pagetitles.php');
$page_title = ET_DETAILS_PAGE;
?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" 
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
        integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" 
        crossorigin="anonymous">
  <title>
    <?= $page_title ?>
  </title>
</head>

<body>
  <?php
  require_once('navmenu.php');
  ?>
  <div class="card">
    <div class="card-body">
      <h1>
        <?= $page_title ?>
      </h1>
      <hr />
      <?php
      if (isset($_GET['id'])):

        require_once('dbconnection.php');
        require_once('queryutils.php');
        require_once('exercisetrackerfileconstants.php');

        $id = $_GET['id'];

        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
          or trigger_error('Error connecting to MySQL server for '
            . DB_NAME, E_USER_ERROR);

        $query = "SELECT * FROM exercise_user WHERE id = ?";

        $result = parameterizedQuery($dbc, $query, 'i', $id)
          or trigger_error(mysqli_error($dbc), E_USER_ERROR);

        if (mysqli_num_rows($result) == 1):

          $row = mysqli_fetch_assoc($result);

          $profile_image_file = $row['image_file'];

          if (empty($profile_image_file)):
            $profile_image_file = ET_UPLOAD_PATH . ET_DEFAULT_PROFILE_FILE_NAME;

          endif;

          ?>
          <h1>
            <?= $row['user_name'] ?>
          </h1>
          <div class="row">
            <div class="col-4">
              <img src="<?= $profile_image_file ?>" class="img-thumbnail" style="max-height: 200px;" alt="Profile image">
            </div>
            <div class="col">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <th scope="row">First Name</th>
                    <td>
                      <?= $row['first_name'] ?>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Last Name</th>
                    <td>
                      <?= $row['last_name'] ?>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Gender</th>
                    <td>
                      <?= $row['gender'] ?>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Birthdate</th>
                    <td>
                      <?= $row['birthdate'] ?>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Weight</th>
                    <td>
                      <?= $row['weight'] ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <?php

          if (
            isset($_SESSION['user_access_privileges'], $_SESSION['user_id'])
            && ($_SESSION['user_access_privileges'] == 'admin'
              || $_SESSION['user_id'] == $row['id'])
          ):
            ?>
            <hr />
            <div class='nav-link'>If you would like to change any of the details of this profile, feel free to <a
                href='editprofile.php?id_to_edit=<?= $row['id'] ?>'> edit it</a></div>
          <?php
          endif;
        else:
          ?>
          <h3>No Profile Details :-(</h3>
          <?php
        endif;
      else:
        ?>
        <h3>No Profile Details :-(</h3>
        <?php
      endif;
      ?>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
          integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
          crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
          integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
          crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
          integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
          crossorigin="anonymous"></script>
</body>

</html>