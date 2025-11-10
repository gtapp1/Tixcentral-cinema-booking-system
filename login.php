<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = strtolower(trim(post('email','')));
  $pass = post('password','');
  if (!$email || !$pass) {
    set_flash('error','Enter email and password.');
    redirect('/draft2/login.php');
  }
  $stmt = $mysqli->prepare("SELECT * FROM users WHERE email=?");
  $stmt->bind_param('s', $email); $stmt->execute();
  $u = $stmt->get_result()->fetch_assoc();
  if ($u && password_verify($pass, $u['password_hash'])) {
    $_SESSION['user'] = ['id'=>$u['id'], 'name'=>$u['name'], 'email'=>$u['email']];
    $to = $_SESSION['return_to'] ?? '/draft2/';
    unset($_SESSION['return_to']);
    redirect($to);
  } else {
    set_flash('error','Invalid credentials.');
    redirect('/draft2/login.php');
  }
}
?>
<div class="container py-5" style="max-width:520px;">
  <h1 class="tc-heading mb-3">Login</h1>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" class="form-control" name="password" required>
    </div>
    <button class="btn btn-danger w-100" type="submit">Login</button>
  </form>
  <div class="mt-3 text-secondary">No account? <a href="/draft2/register.php">Sign up</a></div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
