<?php
require_once "config.php";

function insertSeeker($name,$email,$phone,$password){
    $sql = "INSERT INTO users(name,email,passwordhash,phone,role) VALUES('".$name."','".$email."','".md5($password)."','".$phone."','seeker')";
    mysqli_query($GLOBALS['conn'],$sql);
    return mysqli_insert_id($GLOBALS['conn']);
}

function loginSeeker($email,$password){
    $sql = "SELECT * FROM users WHERE email='".$email."' AND passwordhash='".md5($password)."' AND role='seeker'";
    $result = mysqli_query($GLOBALS['conn'],$sql);
    return mysqli_fetch_assoc($result);
}

function getJobs(){
    $sql = "SELECT j.*,c.name as catname FROM jobs j, categories c WHERE j.categoryid=c.id AND j.status='active'";
    return mysqli_query($GLOBALS['conn'],$sql);
}

function filterJobs($cat,$loc,$type){
    $sql = "SELECT j.*,c.name as catname FROM jobs j, categories c WHERE j.categoryid=c.id AND j.status='active'";
    if($cat) $sql .= " AND j.categoryid='".$cat."'";
    if($loc) $sql .= " AND j.location LIKE '%".$loc."%'";
    if($type) $sql .= " AND j.jobtype='".$type."'";
    return mysqli_query($GLOBALS['conn'],$sql);
}
?>