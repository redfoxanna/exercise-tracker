<?php
  require_once('authorizeaccess.php');
  require_once('pagetitles.php');
  $page_title = ET_REMOVE_USER_PAGE;
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
  <?php
    require_once('navmenu.php');
  ?>
    <div class="card">
      <div class="card-body">
        <h1>Remove a Profile</h1>
        <?php
            require_once('dbconnection.php');
            require_once('profileimagefileutil.php');
            require_once('queryutils.php');

            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or trigger_error('Error connecting to MySQL server for DB_NAME.', E_USER_ERROR);

            if (isset($_POST['delete_profile_submission']) && isset($_POST['id'])):

                $id = $_POST['id'];

                // Query image file from DB
                $query = "SELECT image_file FROM exercise_user WHERE id = ?";

                $result = parameterizedQuery($dbc, $query, 'i', $id)
                        or trigger_error('Error querying database exercise_user', E_USER_ERROR);

                if (mysqli_num_rows($result) == 1)
                {
                    $row = mysqli_fetch_assoc($result);

                    $profile_image_file = $row['image_file'];

                    if (!empty($profile_image_file))
                    {
                        removeProfileImageFile($profile_image_file);
                    }
                }
                // Query to delete the user by admin from the application
                $query = "DELETE FROM exercise_user WHERE id = ?";

                $result = parameterizedQuery($dbc, $query, 'i', $id)
                        or trigger_error('Error querying database exercise_user', E_USER_ERROR);

                header("Location: " . dirname($_SERVER['PHP_SELF']));

            elseif (isset($_POST['do_not_delete_profile_submission'])):

                header("Location: " . dirname($_SERVER['PHP_SELF']));

            elseif (isset($_GET['id_to_delete'])):
        ?>
                <h3 class="text-danger">Confirm Deletion of the Following Profile:</h3><br/>
        <?php
                $id = $_GET['id_to_delete'];

                $query = "SELECT * FROM exercise_user WHERE id = ?";

                $result = parameterizedQuery($dbc, $query, 'i', $id)
                        or trigger_error('Error querying database movieListing', E_USER_ERROR);

                if (mysqli_num_rows($result) == 1)
                {
                    $row = mysqli_fetch_assoc($result);

                    $profile_image_file = $row['image_file'];

                    if (empty($profile_image_file))
                    {
                        $profile_image_file = ET_UPLOAD_PATH . ET_DEFAULT_PROFILE_FILE_NAME;
                    }

            ?>
            <h1><?=$row['user_name']?></h1>
            <div class="row">
              <div class="col-2">
                <img src="<?=$profile_image_file?>" class="img-thumbnail" style="max-height: 200px;" alt="Movie image">
              </div>
              <div class="col">
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th scope="row">First Name</th>
                      <td><?=$row['first_name']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Last Name</th>
                      <td><?=$row['last_name']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Gender</th>
                      <td><?=$row['gender']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Birthdate</th>
                      <td><?=$row['birthdate']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Weight</th>
                      <td><?=$row['weight']?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <p>
            <form method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
            <div class="form-group row">
          
              <button class="btn btn-danger mr-2" type="submit" name="delete_profile_submission">Delete Profile</button>
              <button class="btn btn-success" type="submit" name="do_not_delete_profile_submission">Don't Delete</button>
            </div>
            <input type="hidden" name="id" value="<?= $id ?>">
          </div>

            </form>
            <?php
                }
                else
                {
                ?>
            <h3>No Profile Details :-(</h3>
                <?php
                }

            else: // Unintended page link -  No movie to remove, link back to index

                header("Location: " . dirname($_SERVER['PHP_SELF']));

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