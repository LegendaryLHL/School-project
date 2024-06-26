<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = htmlspecialchars($_POST["role"]);

    if (empty($role)) callBack(false, "empty role");

    try {
        require_once 'dbh.inc.php';
        require_once 'login_model.inc.php';
        require_once 'login_contr.inc.php';
        $_SESSION['role'] = $role;
        if ($role == "Student") {
            $id = htmlspecialchars($_POST["student_id"]);
            $class = htmlspecialchars($_POST["student_class"]);

            if (is_input_empty_student($id, $class)) callBack(false, "empty name or class");

            $result = get_student($conn, $id, $class);
            if (is_username_wrong($result)) callBack(false, "name or class not found");

            $name = get_name_by_id($conn, $id);
            $_SESSION['name'] = $name['nama_murid'];
            $_SESSION['class'] = $class;
            $_SESSION['id'] = $id;
            callBack(true, "login sucess");
        } elseif ($role == "Teacher") {
            $id_guru = htmlspecialchars($_POST["teacher_id"]);
            $password = htmlspecialchars($_POST["password"]);

            if (is_input_empty($id_guru, $password)) callBack(false, "empty id or password");

            $result = get_user($conn, $id_guru);
            if (is_username_wrong($result)) callBack(false, "id not found");

            if (!is_username_wrong($result) && is_password_wrong($password, $result['password_guru'])) callBack(false, "wrong password");

            $_SESSION['username'] = $result['nama_guru'];
            $_SESSION['password'] = $password;
            callBack(true, "login sucess");
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
    callBack(false, "end of condition");
} else {
    callBack(false, "not allowed?(not post)");
}

function callBack($sucess, $message)
{
    $respond = array();
    $respond['success'] = $sucess;
    if (!$sucess) $respond['message'] = $message;

    echo json_encode($respond);
    die();
}
