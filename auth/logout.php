<?php
session_start();
session_destroy();
header('Location: /karyawan-wa/auth/login.php');