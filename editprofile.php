<?php
require_once('pagetitles.php');
$page_title = ET_EDIT_PAGE;
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= $page_title ?></title>
    <link rel="stylesheet" 
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
          integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" 
          crossorigin="anonymous">
  </head>
  <body>
    <?php require_once('navmenu.php'); ?>
    <div class="card">
      <div class="card-body">
        <h1>
          <?= $page_title ?>
        </h1>
        <hr />
          <?php 
            require_once('dbconnection.php');
          
            // If id_to_edit is set, connect to db and query
            if (isset($_GET['id_to_edit'])) {

              $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or trigger_error('Error connecting to MySQL server for '
                    . DB_NAME, E_USER_ERROR);

              $id_to_edit = $_GET['id_to_edit'];

              require_once('queryutils.php');

              $query = "SELECT * FROM exercise_user WHERE id = ?";

              $result = parameterizedQuery($dbc, $query, 'i', $id_to_edit)
                or trigger_error(mysqli_error($dbc), E_USER_ERROR);

              //If result, assign values
              if (mysqli_num_rows($result) == 1) {

                $row = mysqli_fetch_assoc($result);

                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $gender = $row['gender'];
                $birthdate = $row['birthdate'];
                $weight = $row['weight'];
                $user_name = $row['user_name'];
                $password = $row['password_hash'];
                $profile_image_file = $row['image_file'];

              if (empty($profile_image_file)) {
                $profile_image_file_displayed = ET_UPLOAD_PATH . ET_DEFAULT_PROFILE_FILE_NAME;
              } else {
                $profile_image_file_displayed = $profile_image_file;
              }
            
            }
            // If editing, assign new values & sanitize 
            } elseif (isset($_POST['edit_profile_submission'], $_POST['first_name'], $_POST['last_name'], $_POST['gender'], $_POST['birthdate'], $_POST['weight'])) {
                  $first_name = $_POST['first_name'];
                  $last_name = $_POST['last_name'];
                  $gender = $_POST['gender'];
                  $birthdate = $_POST['birthdate'];
                  $weight = $_POST['weight'];
                  $id_to_update = $_POST['id'];
                  

                  $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
                  $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);

                  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or trigger_error('Error connecting to MySQL server for '
                    . DB_NAME, E_USER_ERROR);

          

                $query = "UPDATE exercise_user SET first_name = ?, last_name = ?, gender = ?, birthdate = ?, weight = ?  WHERE id = ?";

                require_once('queryutils.php');

                parameterizedQuery($dbc, $query, 'ssssii', $first_name, $last_name, $gender, $birthdate, $weight, $id_to_update);

                if (mysqli_errno($dbc)) {
                  trigger_error('Error querying database exercise_user: Failed to update profile', E_USER_ERROR);
                }


                $nav_link = 'viewprofile.php?id=' . $id_to_edit;

                header("Location: $nav_link");
                exit();
              } 


      else // Unintended page link -  No profile to edit, link back to index
      {
        header("Location: index.php");
        exit();
      }
      
      ?>
      <h1>
        <?= $_SESSION['user_name'] ?>
      </h1>
      <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id_to_edit ?>" />
        <div class="form-group">
          <label for="first_name">First Name:</label>
          <input type="text" name="first_name" class="form-control" value="<?= $first_name ?>" />
        </div>
        <div class="form-group">
          <label for="last_name">Last Name:</label>
          <input type="text" name="last_name" class="form-control" value="<?= $last_name ?>" />
        </div>
        <div class="form-group">
          <label for="gender">Gender:</label>
          <select name="gender" class="form-control">
            <option value="male" <?= ($gender == 'male') ? 'selected' : '' ?>>male</option>
            <option value="female" <?= ($gender == 'female') ? 'selected' : '' ?>>female</option>
            <option value="non-binary" <?= ($gender == 'non-binary') ? 'selected' : '' ?>>non-binary</option>
          </select>
        </div>
        <div class="form-group">
          <label for="birthdate">Birthdate:</label>
          <input type="date" name="birthdate" class="form-control" value="<?= $birthdate ?>" />
        </div>
        <div class="form-group">
          <label for="weight">Weight:</label>
          <input type="number" name="weight" class="form-control" value="<?= $weight ?>" />
        </div>
        <div class="form-group">
          <label for="profile_image">Profile Image:</label>
          <input type="file" name="profile_image" class="form-control-file" />
        </div>
        <button type="submit" class="btn btn-primary" name="edit_profile_submission">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    // JavaScript for disabling form submissions if there are invalid fields
    (function () {
      'use strict';
      window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
          form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classlist.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
          integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
          crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
          integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
          crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
          integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
          crossorigin="anonymous"></script>
</body>

</html>