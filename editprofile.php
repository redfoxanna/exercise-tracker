<?php
require_once('authorizeaccess.php');
require_once('pagetitles.php');
$page_title = ET_EDIT_PAGE;
?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
    integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <title>
    <?= $page_title ?>
  </title>
</head>

<body>
  <?php require_once('navmenu.php');
  require_once('dbconnection.php');
  require_once('exercisetrackerfileconstants.php');
  require_once('profileimagefileutil.php');

  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or trigger_error('Error connecting to MySQL server for '
      . DB_NAME, E_USER_ERROR);
  ?>

  <div class="card">
    <div class="card-body">
      <h1>
        <?= $page_title ?>
      </h1>
      <hr />
      <?php
      if (isset($_GET['id_to_edit'])) {

        $id = $_GET['id_to_edit'];

        require_once('queryutils.php');

        $query = "SELECT * FROM exercise_user WHERE id = ?";

        $result = parameterizedQuery($dbc, $query, 'i', $id_to_edit)
          or trigger_error(mysqli_error($dbc), E_USER_ERROR);

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
        }
      } elseif (isset($_POST['edit_profile_submission'], $_POST['first_name'], $_POST['last_name'], $_POST['gender'], $_POST['birthdate'], $_POST['weight'], $_POST['image_file'], )) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $gender = $_POST['gender'];
        $birthdate = $_POST['birthdate'];
        $weight = $_POST['weight'];
        $id_to_update = $_POST['id_to_update'];
        $profile_image_file = $_POST['profile_image_file'];

        $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
        $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);

        if (empty($profile_image_file)) {
          $profile_image_file_displayed = ET_UPLOAD_PATH . ET_DEFAULT_PROFILE_FILE_NAME;
        } else {
          $profile_image_file_displayed = $profile_image_file;
        }

        /*
        Here is where we will deal with the file by calling validateMovieImageFile().
        This function will validate that the movie image file is not greater than 128
        characters, is the right image type (jpg/png/gif), and not greater than 512KB.
        This function will return an empty string ('') if the file validates successfully,
        otherwise, the string will contain error text to be output to the web page before
        redisplaying the form.
        */

        $file_error_message = validateProfileImageFile();

        if (empty($file_error_message)) {
          $profile_image_file_path = addProfileImageFileReturnPathLocation();

          // IF new image selected, set it to be updated in the database.
          if (!empty($profile_image_file_path)) {
            // IF replacing an image (other than the default), remove it
            if (!empty($profile_image_file)) {
              removeProfileImageFile($profile_image_file);
            }

            $profile_image_file = $profile_image_file_path;
          }

          $query = "UPDATE exercise_user SET first_name = ?, last_name = ?, gender = ?, birthdate = ?, weight = ?, image_file = ? WHERE id = ?";

          require_once('queryutils.php');

          parameterizedQuery($dbc, $query, 'ssssis', $first_name, $last_name, $gender, $birthdate, $weight, $profile_image_file);

          if (mysqli_errno($dbc)) {
            trigger_error('Error querying database movieListing: Failed to update profile', E_USER_ERROR);
          }


          $nav_link = 'viewprofile.php?id=' . $id_to_update;

          header("Location: $nav_link");
          exit();
        } else {
          // echo error message 
          echo "<h5><p class='text-danger'>" . $file_error_message .
            "</p></h5>";
        }

      } else // Unintended page link -  No movie to edit, link back to index
      {
        header("Location: index.php");
        exit();
      }
      ?>
      <h1>
        <?= $row['user_name'] ?>
      </h1>
      <form method="post" action="editprofile.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $row['id'] ?>" />
        <div class="form-group">
          <label for="first_name">First Name:</label>
          <input type="text" name="first_name" class="form-control" value="<?= $row['first_name'] ?>" />
        </div>
        <div class="form-group">
          <label for="last_name">Last Name:</label>
          <input type="text" name="last_name" class="form-control" value="<?= $row['last_name'] ?>" />
        </div>
        <div class="form-group">
          <label for="gender">Gender:</label>
          <select name="gender" class="form-control">
            <option value="Male" <?= ($row['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($row['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
            <option value="Non-binary" <?= ($row['gender'] == 'Non-binary') ? 'selected' : '' ?>>Non-binary</option>
          </select>
        </div>
        <div class="form-group">
          <label for="birthdate">Birthdate:</label>
          <input type="date" name="birthdate" class="form-control" value="<?= $row['birthdate'] ?>" />
        </div>
        <div class="form-group">
          <label for="weight">Weight (in kg):</label>
          <input type="number" name="weight" class="form-control" value="<?= $row['weight'] ?>" />
        </div>
        <div class="form-group">
          <label for="profile_image">Profile Image:</label>
          <input type="file" name="profile_image" class="form-control-file" />
        </div>
        <button type="submit" class="btn btn-primary" name="edit_post_submission">Save Changes</button>
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