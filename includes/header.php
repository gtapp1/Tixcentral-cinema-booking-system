<?php require_once __DIR__ . '/auth.php'; $user = current_user(); ?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TixCentral</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/draft2/assets/css/styles.css" rel="stylesheet">
</head>
<body class="tc-body">
<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top border-danger-subtle">
  <div class="container-fluid">
    <a class="navbar-brand tc-logo-wrapper" href="/draft2/">
      <img src="/draft2/assets/images/logo.svg" alt="TixCentral" class="tc-logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link fs-6 py-1" href="/draft2/now_showing.php">Movies</a></li>
        <li class="nav-item"><a class="nav-link fs-6 py-1" href="/draft2/history.php">Booking History</a></li>
        <li class="nav-item"><a class="nav-link fs-6 py-1" href="/draft2/about.php">About Us</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if ($user): ?>
          <span class="user-name-pill" style="pointer-events:none; cursor:default; color:#fff;">Hello, <?=esc($user['name'])?></span>
          <a href="/draft2/logout.php" class="btn btn-danger btn-sm">Logout</a>
        <?php else: ?>
          <a href="/draft2/login.php" class="btn btn-outline-light btn-sm fs-6">Login</a>
          <a href="/draft2/register.php" class="btn btn-danger btn-sm fs-6">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<?php if ($msg = get_flash('error')): ?>
  <div class="alert alert-danger rounded-0 mb-0"><?=esc($msg)?></div>
<?php endif; ?>
<?php if ($msg = get_flash('success')): ?>
  <div class="alert alert-success rounded-0 mb-0"><?=esc($msg)?></div>
<?php endif; ?>
<main class="tc-main">
