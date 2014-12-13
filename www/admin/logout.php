<?php
session_start();
$_SESSION['login'] = '';
unset($_SESSION);
header('Location:login.php');
