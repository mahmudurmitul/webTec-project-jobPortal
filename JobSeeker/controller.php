<?php
session_start();
require_once "model.php";

if(isset($_GET['action']) && $_GET['action']=="getJobs"){
    $result = getJobs();
    $jobs = array();
    while($row = mysqli_fetch_assoc($result)){
        $jobs[] = $row;
    }
    header("Content-Type: application/json");
    echo json_encode($jobs);
    exit();
}

if(isset($_GET['action']) && $_GET['action']=="filterJobs"){
    $cat = $_GET['category'] ?? '';
    $loc = $_GET['location'] ?? '';
    $typ = $_GET['type'] ?? '';
    $result = filterJobs($cat,$loc,$typ);
    $jobs = array();
    while($row = mysqli_fetch_assoc($result)){
        $jobs[] = $row;
    }
    header("Content-Type: application/json");
    echo json_encode($jobs);
    exit();
}

$errors = array();
$msg = "";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    if(empty($name)) $errors[] = "Name required";
    if(empty($email)) $errors[] = "Email required";
    if(empty($phone)) $errors[] = "Phone required";
    if(empty($password)) $errors[] = "Password required";
    
    if(count($errors)==0){
        $userid = insertSeeker($name,$email,$phone,$password);
        $_SESSION['seeker_id'] = $userid;
        $_SESSION['seeker_name'] = $name;
        header("Location: index.php?msg=Registered");
        exit();
    }
}

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = loginSeeker($email,$password);
    if($user){
        $_SESSION['seeker_id'] = $user['id'];
        $_SESSION['seeker_name'] = $user['name'];
        header("Location: index.php?msg=Login Success");
        exit();
    } else {
        $errors[] = "Wrong email/password";
    }
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
    exit();
}

$jobs_result = getJobs();
?>
