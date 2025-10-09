<?php
  $curr_db = "loophish";
  $conn = mysqli_connect("127.0.0.1","root","",$curr_db);

  if (mysqli_connect_errno()) {
    die("DB connection failed!");
  } 
?>