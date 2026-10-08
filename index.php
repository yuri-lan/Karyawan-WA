<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: auth/login.php');
elseif($_SESSION['role']==='admin') header('Location: admin/index.php');
else header('Location: absensi/index.php');