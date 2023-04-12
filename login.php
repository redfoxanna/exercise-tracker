<?php
    session_start();
    require_once('pagetitles.php');
    $page_title = ET_LOGIN_PAGE;
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
        <h1><?= $page_title ?></h1>
        <hr/>
        <?php
        if (empty($_SESSION['user_id']) && isset($_POST['login_submission']))
        {
            // Get user name and password
            $user_name = $_POST['user_name'];
            $password = $_POST['password'];

            if (!empty($user_name) && !empty($password))
            {
                require_once('dbconnection.php');

                require_once('queryutils.php');

                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                        or trigger_error(
                        'Error connecting to MySQL server for' . DB_NAME,
                        E_USER_ERROR );

                // Check if user already exists
                $query = "SELECT * FROM exercise_user WHERE user_name = ?";

                $results = parameterizedQuery($dbc, $query, 's', $user_name)
                        or trigger_error(mysqli_error($dbc), E_USER_ERROR);

                // IF user was found, validate password
                if (mysqli_num_rows($results) == 1)
                {
                    $row = mysqli_fetch_array($results);

                    if (password_verify($password, $row['password_hash']))
                    {
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['user_access_privileges'] = $row['access_privileges'];

                        // Redirect to the home page
                        $home_url = dirname($_SERVER['PHP_SELF']);
                        header('Location: ' . $home_url);
                    }
                    else
                    {
                        echo "<h4><p class='text-danger'>An incorrect user name 
                              or password was entered.</p></h4><hr/>";
                    }
                }
                else if(mysqli_num_rows($results) == 0) // User does not exist
                {
                    echo "<h4><p class='text-danger'>An account does not exist for this username:"
                        . "<span class='font-weight-bold'> ($user_name)</span>. "
                        . "Please use a different user name.</p></h4><hr/>";
                }
                else
                {
                    echo "<h4><p class='text-danger'>Something went terribly wrong!</p></h4><hr/>";
                }
            }
            else
            {
                // Output error message
                echo "<h4><p class='text-danger'>You must enter both a user name and password.</p></h4><hr/>";
            }
        }
        if (empty($_SESSION['user_id'])):
        ?>
          <form class="needs-validation" novalidate method="POST"
                action="<?= $_SERVER['PHP_SELF'] ?>">
            <div class="form-group row">
              <label for="user_name" class="col-sm-2 col-form-label-lg">User
                  Name</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" id="user_name"
                    name="user_name" placeholder="Enter a user name"
                    required>
                <div class="invalid-feedback">
                    Please provide a valid user name.
                </div>
              </div>
            </div>
              <div class="form-group row">
                <label for="password" class="col-sm-2 col-form-label-lg">Password</label>
                <div class="col-sm-4">
                  <input type="password" class="form-control"
                      id="password" name="password"
                      placeholder="Enter a password" required>
                  <div class="invalid-feedback">
                      Please provide a valid password.
                  </div>
                </div>
              </div>
              <button class="btn btn-primary" type="submit"
                  name="login_submission">Log In
              </button>
          </form>
        <?php
        elseif (isset($_SESSION['user_name'])):
            echo "<h4><p class='text-success'>You are logged in as:
                  <strong>{$_SESSION['user_name']}</strong>.</p></h4>";
        endif;
        ?>
      </div>
    </div>
  <script>
      // JavaScript for disabling form submissions if there are invalid fields
      (function() {
          'use strict';
          window.addEventListener('load', function() {
              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.getElementsByClassName('needs-validation');
              // Loop over them and prevent submission
              var validation = Array.prototype.filter.call(forms, function(form) {
                  form.addEventListener('submit', function(event) {
                      if (form.checkValidity() === false) {
                          event.preventDefault();
                          event.stopPropagation();
                      }
                      form.classList.add('was-validated');
                  }, false);
              });
          }, false);
      })();
  </script>
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