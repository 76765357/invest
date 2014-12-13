<?php
define("APP_PATH", "../../app/"); 
header("Content-Type:text/html; charset=utf-8");   
require_once(APP_PATH . 'initphp/initphp.php'); //导入配置文件-必须载入
require_once(APP_PATH . 'conf/comm.conf.php'); //公用配置
echo 1;

exit();
$user=$_POST['username'];
$pw=$_POST['password'];
$pw=md5($pw);
session_start();

$sql = "select * from user where name='$user' and pw='$pw'";
$result = $db->fetchOne($sql);
if($result['name'])
{
    $_SESSION['user']=$result['name'];
    $_SESSION['type']=$result['type'];
    header('Location:index.php');
}else{
    header('Location:login.php');
}

?>

