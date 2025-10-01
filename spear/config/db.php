<?php
  $curr_db = "loophish";
  $conn = mysqli_connect("loophish.local","root","",$curr_db);

  if (mysqli_connect_errno()) {
    die("DB connection failed!");
  } 
?>