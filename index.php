<?php 

require_once 'functions.php';
require_once 'config/dbConnection.php';
require_once 'router.php';

$config = require_once 'config.php';

$db = new dbConnection($config['database']);

$userId = $_GET['user_id']; // ex. http://localhost:3000/?user_id=1;

$query = 'SELECT * FROM users WHERE user_id = :user_id';
$users = $db->query($query, [':user_id' => $userId])->fetch();

dd($users);

?>