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
  <link rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf"
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

        // Get exercise_user information from the db 
        $user_query = "SELECT * FROM exercise_user WHERE id = ?";

        $user_result = parameterizedQuery($dbc, $user_query, 'i', $id)
          or trigger_error(mysqli_error($dbc), E_USER_ERROR);

        if (mysqli_num_rows($user_result) == 1):

          $row = mysqli_fetch_assoc($user_result);

          $profile_image_file = $row['image_file'];

          if (empty($profile_image_file)) {
            $default_profile_image = ET_UPLOAD_PATH . ET_DEFAULT_PROFILE_FILE_NAME;
            $profile_image_file = $default_profile_image;
          }

          // Retrieve the user's exercise log from the exercise_log table
          $exercise_query = "SELECT * FROM exercise_log WHERE user_id = ? ORDER BY exercise_date DESC LIMIT 15";
          
          $exercise_result = parameterizedQuery($dbc, $exercise_query, 'i', $id)
          or trigger_error(mysqli_error($dbc), E_USER_ERROR);

          if (mysqli_num_rows($exercise_result) > 0):
            $exercise_data = array();
          
            while ($exercise_row = mysqli_fetch_assoc($exercise_result)) {
              $exercise_data[] = $exercise_row;
              $exercise_id = $exercise_row['id'];
            }
          
            ?>
           <div class='nav-link' style='text-align: left' id='edit-profile-link'>If you would like to change any of the details of this profile, feel free to <a
                href='editprofile.php?id_to_edit=<?= $row['id'] ?>'> edit it</a></div> 
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
                  <tr>
                  </tr>
                </tbody>
              </table>
            </div> 
          </div>
          
          <hr/>
          <h2>Exercise Log</h2>
            <div class="row">
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th scope="col">Date</th>
                      <th scope="col">Type</th>
                      <th scope="col">Heartrate</th>
                      <th scope="col">Time (minutes)</th>
                      <th scope="col">Calories Burned</th>
                      <th scope="col"></th>
                    </tr>
                    <?php foreach ($exercise_data as $exercise_row): ?>
                    <?php $exercise_id = $exercise_row['id']; ?>
                      <tr>
                        <td><?= $exercise_row['exercise_date'] ?></td>
                        <td><?= $exercise_row['exercise_type'] ?></td>                    
                        <td><?= $exercise_row['heartrate'] ?></td>
                        <td><?= $exercise_row['exercise_time'] ?></td>
                        <td><?= $exercise_row['calories_burned'] ?></td>
                        <td><a class='nav-link' href='removeexercise.php?id_to_delete=<?= $exercise_id ?>'><i class='fas fa-trash-alt'></i></a></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>


          <?php

          if (
            isset($_SESSION['user_access_privileges'], $_SESSION['user_id'])
            && ($_SESSION['user_access_privileges'] == 'admin'
              || $_SESSION['user_id'] == $row['id'])
          ):
            ?>
            <hr />
            
          <?php
          endif;
        else:
          ?>
          <h3>You updated your profile!</h3>
          <?php
        endif;
      
        ?>
        <h3>Thanks for being wonderful!</h3>
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