<?php
require_once('pagetitles.php');
$page_title = ET_ADD_PAGE;
require_once('authorizeaccess.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        <?= $page_title ?>
    </title>
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
            <h1>
                <?= $page_title ?>
            </h1>
            <hr />
            <?php
            $display_add_exercise_form = true;
            $exercise_type = "";
            $exercise_date = "";
            $exercise_time = 0;
            $heartrate = 0;

            // Test if user set values and if so, connect and add to the database
            if (
                isset($_POST['add_exercise_submission'], $_POST['exercise_type'],
                $_POST['exercise_date'], $_POST['exercise_time'], $_POST['heartrate'])
            ) {
                require_once('dbconnection.php');
                require_once('queryutils.php');


                $exercise_type = $_POST['exercise_type'];
                $exercise_date = $_POST['exercise_date'];
                $exercise_time = $_POST['exercise_time'];
                $heartrate = $_POST['heartrate'];

                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                        or trigger_error(
                            'Error connecting to MySQL server for' . DB_NAME,
                            E_USER_ERROR
                        );

                // Retrieve the user's gender, weight from the database
                $user_id = $_SESSION['user_id'];

                $query = "SELECT * FROM exercise_user WHERE id = $user_id";

                $result = mysqli_query($dbc, $query);

                $row = mysqli_fetch_assoc($result);

                $gender = $row['gender'];
                $weight = $row['weight'];

                // Get the birthdate from the database in yyyy-mm-dd format
                $birthdate = $row['birthdate'];

                // Calculate the difference between the birthdate and the current date
                $diff = strtotime('now') - strtotime($birthdate);

                // Calculate the age in years
                $age = floor($diff / (365 * 60 * 60 * 24));

                // Use the gender to calculate the calorie burn
                if ($gender == 'male') {
                    $calories_burned = ((-55.0969 + (0.6309 * $heartrate) + (0.090174 * $weight) + (0.2017 * $age)) / 4.184) * $exercise_time;
                } elseif ($gender == 'female') {
                    $calories_burned = ((-20.4022 + (0.4472 * $heartrate) - (0.057288 * $weight) + (0.074 * $age)) / 4.184) * $exercise_time;
                } else {
                    $calories_burned = ((-37.7495 + (0.5391 * $heartrate) + (0.01644 * $weight) + (0.1379 * $age)) / 4.184) * $exercise_time;
                }
        

                $query = "INSERT INTO exercise_log (user_id, exercise_type, exercise_date, exercise_time, heartrate, calories_burned) "
                        . " VALUES (?, ?, ?, ?, ?, ?)";

                $results = parameterizedQuery(
                        $dbc,
                        $query,
                        'issiii',
                        $user_id,
                        $exercise_type,
                        $exercise_date,
                        $exercise_time,
                        $heartrate,
                        $calories_burned
                    )
                        or trigger_error(mysqli_error($dbc), E_USER_ERROR);

                    $display_add_exercise_form = false;
                    ?>
                    <h3 class="text-info">The Following Exercise Details were Added:</h3><br />

                    <h1>
                        <?= "$exercise_type" ?>
                    </h1>
                        <div class="col">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Date</th>
                                        <td>
                                            <?= $exercise_date ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Duration (minutes)</th>
                                        <td>
                                            <?= $exercise_time ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Heartrate (average)</th>
                                        <td>
                                            <?= $heartrate ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Calories burned</th>
                                        <td>
                                            <?= $calories_burned ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr />
                    <p>Would you like to <a href='<?= $_SERVER['PHP_SELF'] ?>'> add another exercise</a>?</p>
                    <?php
            }

            if ($display_add_exercise_form) {
                echo var_dump($_SESSION);
                ?>
                <form enctype="multipart/form-data" class="needs-validation"
                        novalidate method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <form class="needs-validation" novalidate>
                                <div class="form-group">
                                    <label for="exercise_type">Exercise:</label>
                                    <select id="exercise_type" name="exercise_type" class="form-control" required>
                                        <option value="">Select an exercise</option>
                                        <option value="running">Running</option>
                                        <option value="cycling">Cycling</option>
                                        <option value="swimming">Swimming</option>
                                        <option value="weightlifting">Weightlifting</option>
                                        <option value="walking">Walking</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select an exercise.
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exercise_date">Date:</label>
                                    <input type="date" id="exercise_date" name="exercise_date" class="form-control" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid date.
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exercise_time">Time (in minutes):</label>
                                    <input type="number" id="exercise_time" name="exercise_time" min="1" 
                                            class="form-control" placeholder="Exercise time (in minutes)" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid time.
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="heartrate">Average Heart Rate:</label>
                                    <input type="number" id="heartrate" name="heartrate" min="1" class="form-control"
                                        placeholder="Average heart rate" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid heart rate.
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" name="add_exercise_submission">Log Exercise</button>
                            </form>
                        </div>
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
                                    form.classList.add('was-validated');
                                }, false);
                            });
                        }, false);
                    })();
                </script>
                <?php
            } 
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