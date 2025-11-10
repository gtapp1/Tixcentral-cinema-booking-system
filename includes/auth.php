<?php
require_once __DIR__ . '/../config.php';

function current_user() {
  return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function is_logged_in() {
  return current_user() !== null;
}

function require_login($redirect_to = null) {
  if (!is_logged_in()) {
    if (!$redirect_to) { $redirect_to = $_SERVER['REQUEST_URI'] ?? '/draft2/'; }
    $_SESSION['return_to'] = $redirect_to;
    set_flash('error','Please login to continue.');
    redirect('/draft2/login.php');
  }
}
