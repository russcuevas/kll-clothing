<?php
include '../database/connection.php';
session_start();

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['errorMessage'] = 'Please fill in both email and password.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $sql = "SELECT * FROM tbl_users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['successMessage'] = 'Login successful! Welcome back ' . $user['first_name'];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            $_SESSION['errorMessage'] = 'Incorrect password.';
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        $_SESSION['errorMessage'] = 'No user found with that email.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
