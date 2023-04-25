<?php
  require_once('authorizeaccess.php');
  require_once('pagetitles.php');
  $page_title = ET_REMOVE_PAGE;
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
        <h1>Remove an exercise</h1>
        <?php
            require_once('dbconnection.php');
            require_once('queryutils.php');

            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or trigger_error('Error connecting to MySQL server for DB_NAME.', E_USER_ERROR);

            if (isset($_POST['delete_exercise_submission']) && isset($_POST['id'])):

                $id = $_POST['id'];

                // Query db wit id to display exercise to be removed
                $query = "DELETE FROM exercise_log WHERE id = ?";

                $result = parameterizedQuery($dbc, $query, 'i', $id)
                        or trigger_error('Error querying database ExerciseTracker', E_USER_ERROR);

                header("Location: index.php");

            elseif (isset($_POST['do_not_delete_exercise_submission'])):

                header("Location: index.php");

            elseif (isset($_GET['id_to_delete'])):
        ?>
                <h3 class="text-danger">Confirm Deletion of the Following Exercise Details:</h3><br/>
        <?php
                $id = $_GET['id_to_delete'];

                $query = "SELECT * FROM exercise_log WHERE id = ?";

                $result = parameterizedQuery($dbc, $query, 'i', $id)
                        or trigger_error('Error querying database movieListing', E_USER_ERROR);

                if (mysqli_num_rows($result) == 1)
                {
                    $row = mysqli_fetch_assoc($result);

            ?>
            <div class="col">
            <h1><?=$row['exercise_type']?></h1>
              
                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th scope="row">Date</th>
                      <td><?=$row['exercise_date']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Time (in minutes)</th>
                      <td><?=$row['exercise_time']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Average Heartrate</th>
                      <td><?=$row['heartrate']?></td>
                    </tr>
                    <tr>
                      <th scope="row">Calories Burned</th>
                      <td><?=$row['calories_burned']?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <form method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
            <div class="col ml-5">
            <div class="form-group row">
      <button class="btn btn-danger mr-2" type="submit" name="delete_exercise_submission">Delete Exercise</button>
      <button class="btn btn-success" type="submit" name="do_not_delete_exercise_submission">Don't Delete Exercise</button>
    </div>
                </div>
    <input type="hidden" name="id" value="<?= $id ?>">
  </div>
            </form>
            <?php
                }
                else
                {
                ?>
            <h3>No Exercise Details :-(</h3>
                <?php
                }

            else: // Unintended page link -  No exercise to remove, link back to index

                header("Location: index.php");

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