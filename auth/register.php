<?php
include '../database/connection.php';
session_start();

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($firstName) || empty($lastName) || empty($address) || empty($email) || empty($password)) {
        $_SESSION['errorMessage'] = 'Please fill in all fields.';
    } else {
        $sql = "SELECT * FROM tbl_users WHERE email = '$email'";
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            $_SESSION['errorMessage'] = 'This email is already registered.';
        } else {
            $sql = "INSERT INTO tbl_users (first_name, last_name, address, email, password) 
                    VALUES ('$firstName', '$lastName', '$address', '$email', '$password')";
            if ($conn->exec($sql)) {
                $_SESSION['successMessage'] = 'Registration successful! You can now login.';
            } else {
                $_SESSION['errorMessage'] = 'Something went wrong. Please try again.';
            }
        }
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
