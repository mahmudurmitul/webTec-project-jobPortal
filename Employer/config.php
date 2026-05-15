<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "jobportal_db");
if(!$conn){
    die("Connection Failed:".mysqli_connect_error());
}
function employerOnly(){
     if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'employer') {
        header("Location: login.php");
        exit();
    }
}
function employerOnlyFromViews(){
     if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'employer') {
        header("Location: ../login.php");
        exit();
    }
}
function employerOnlyFromApi() {
    if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'employer') {
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized access"
        ]);
        exit();
    }
}
?>

