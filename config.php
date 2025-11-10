<?php
session_start();

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '1234';
$DB_NAME = 'tixcentral2';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo "Database connection failed: " . htmlspecialchars($mysqli->connect_error);
  exit;
}
$mysqli->set_charset('utf8mb4');

function esc($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function post($k, $d=null) { return isset($_POST[$k]) ? trim($_POST[$k]) : $d; }
function getv($k, $d=null) { return isset($_GET[$k]) ? trim($_GET[$k]) : $d; }
function redirect($url) { header("Location: $url"); exit; }

function rand_ref($len=10) {
  $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  $o=''; for($i=0;$i<$len;$i++) $o .= $chars[random_int(0, strlen($chars)-1)];
  return $o;
}

function price_format($n) {
  return '₱' . number_format((float)$n, 2);
}

function set_flash($k,$v){ $_SESSION['flash'][$k]=$v; }
function get_flash($k){ if(isset($_SESSION['flash'][$k])){$v=$_SESSION['flash'][$k]; unset($_SESSION['flash'][$k]); return $v;} return null;}
