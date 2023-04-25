<?php
session_start();

   // // Not logged in, redirect to unauthorizedaccess.php script
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_access_privileges']))
    {
        header("Location: unauthorizedaccess.php");
        exit();
    }

    //IF NOT admininstrative access AND NOT editing their own details, redirect to unauthorizedaccess.php script
    $id_to_edit = "";

    if (isset($_GET['id_to_edit']))
    {
        $id_to_edit = $_GET['id_to_edit'];
    }
    else if (isset($_POST['id_to_update']))
    {
        $id_to_edit = $_POST['id_to_update'];
    }

    if ($_SESSION['user_access_privileges'] != 'admin' &&
            ($_SESSION['user_id'] != $id_to_edit ))
    {
        header("Location: unauthorizedaccess.php");
        exit();
    }