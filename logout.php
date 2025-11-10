<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['user']);
set_flash('success','Logged out.');
header('Location: /draft2/');
