<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim(post('name',''));
  $email = strtolower(trim(post('email','')));
  $pass = post('password','');
  if (!$name || !$email || !$pass) { set_flash('error','All fields are required.'); redirect('/draft2/register.php'); }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { set_flash('error','Invalid email.'); redirect('/draft2/register.php'); }
  $hash = password_hash($pass, PASSWORD_DEFAULT);
  try {
    $stmt = $mysqli->prepare("INSERT INTO users (name, email, password_hash) VALUES (?,?,?)");
    $stmt->bind_param('sss', $name, $email, $hash); $stmt->execute();
    $_SESSION['user'] = ['id'=>$stmt->insert_id, 'name'=>$name, 'email'=>$email];
    redirect('/draft2/');
  } catch (Throwable $e) {
    set_flash('error','Email already registered.');
    redirect('/draft2/register.php');
  }
}
?>
<div class="container py-5" style="max-width:520px;">
  <h1 class="tc-heading mb-3">Sign Up</h1>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" class="form-control" name="name" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" class="form-control" name="password" required minlength="6">
    </div>
    <button class="btn btn-danger w-100" type="submit">Create Account</button>
  </form>
  <div class="mt-3 text-secondary">Already have an account? <a href="/draft2/login.php">Login</a></div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
