<?php 
if (
  !isset($_SESSION['user_id'], $_SESSION['name'], $_SESSION['role']) ||
  empty($_SESSION['user_id']) ||
  empty($_SESSION['name']) ||
  empty($_SESSION['role'])
) {
  header("Location: /field-service-web/employee/source-code/Webpage/login.php");
  exit();
}
?>