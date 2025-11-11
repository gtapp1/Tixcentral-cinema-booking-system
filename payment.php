<?php require_once __DIR__ . '/includes/header.php'; require_once __DIR__ . '/includes/auth.php'; require_login('/draft2/payment.php'); ?>
<?php
// Initial entry from book.php with selected seats
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['schedule_id']) && isset($_POST['selected_seats']) && !isset($_POST['action'])) {
  $schedule_id = (int)$_POST['schedule_id'];
  $seat_ids = array_values(array_filter(array_map('intval', array_filter(explode(',', $_POST['selected_seats'])))));
  if ($schedule_id <= 0 || count($seat_ids) === 0) { set_flash('error','Please select seats.'); redirect('/draft2/now_showing.php'); }
  $sched = $mysqli->query("SELECT s.*, m.title, b.name AS branch_name FROM schedules s JOIN movies m ON m.id=s.movie_id JOIN branches b ON b.id=s.branch_id WHERE s.id=$schedule_id")->fetch_assoc();
  if (!$sched) { set_flash('error','Invalid schedule.'); redirect('/draft2/now_showing.php'); }
  $_SESSION['pending_booking'] = ['schedule_id'=>$schedule_id,'seat_ids'=>$seat_ids];
}
// Payment submission
if ($_SERVER['REQUEST_METHOD']==='POST' && post('action')==='pay') {
  $pb = $_SESSION['pending_booking'] ?? null;
  if (!$pb) { set_flash('error','Session expired. Please reselect seats.'); redirect('/draft2/now_showing.php'); }
  $schedule_id = (int)$pb['schedule_id'];
  $seat_ids = $pb['seat_ids'];
  $method = post('method');
  $details_masked = '';
  if ($method==='GCASH') {
    $raw = post('gcash_mobile');
    $mobile = preg_replace('/\D+/', '', $raw ?? '');
    if (!preg_match('/^(09\d{9}|639\d{9})$/', $mobile)) {
      set_flash('error','Enter a valid GCash mobile number (09XXXXXXXXX).');
      redirect('/draft2/payment.php');
    }
    $details_masked = 'GCASH ****' . substr($mobile, -4);
  } elseif ($method==='MAYA') {
    $raw = post('maya_mobile');
    $mobile = preg_replace('/\D+/', '', $raw ?? '');
    if (!preg_match('/^(09\d{9}|639\d{9})$/', $mobile)) {
      set_flash('error','Enter a valid Maya mobile number (09XXXXXXXXX).');
      redirect('/draft2/payment.php');
    }
    $details_masked = 'MAYA ****' . substr($mobile, -4);
  } elseif ($method==='CARD') {
    $name = trim(post('card_name'));
    $card = preg_replace('/\D+/','', post('card_number'));
    $exp = preg_replace('/\s+/','', post('card_exp'));
    $cvv = preg_replace('/\D+/','', post('card_cvv'));
    if (strlen($name)<2 || strlen($card)<12 || !preg_match('/^\d{2}\/\d{2}$/',$exp) || strlen($cvv)<3) {
      set_flash('error','Please enter valid card details.');
      redirect('/draft2/payment.php');
    }
    $details_masked = $name . ' • **** **** **** ' . substr($card,-4) . ' • ' . $exp;
  } else {
    set_flash('error','Select a payment method.');
    redirect('/draft2/payment.php');
  }

  // Compute total
  $sched = $mysqli->query("SELECT * FROM schedules WHERE id=$schedule_id")->fetch_assoc();
  if (!$sched) { set_flash('error','Invalid schedule.'); redirect('/draft2/now_showing.php'); }
  $total = (float)$sched['price'] * count($seat_ids);

  // Transaction: verify seats not already booked for this schedule and then insert booking
  $mysqli->begin_transaction();
  try {
    // Check availability
    $in = implode(',', array_map('intval',$seat_ids));
    $q = $mysqli->query("SELECT bs.seat_id FROM booking_seats bs 
      JOIN bookings b ON b.id=bs.booking_id
      WHERE b.schedule_id=$schedule_id AND b.status='CONFIRMED' AND bs.seat_id IN ($in) FOR UPDATE");
    if ($q->num_rows > 0) {
      $mysqli->rollback();
      set_flash('error','Some selected seats have just been booked. Please reselect.');
      redirect('/draft2/book.php?schedule_id='.$schedule_id);
    }

    // Create booking
    $ref = rand_ref(10);
    $stmt = $mysqli->prepare("INSERT INTO bookings (user_id, schedule_id, reference, total_amount, status) VALUES (?,?,?,?, 'PENDING')");
    $uid = current_user()['id'];
    $stmt->bind_param('iisd', $uid, $schedule_id, $ref, $total);
    $stmt->execute();
    $booking_id = $stmt->insert_id;

    // Insert seats
    $ins = $mysqli->prepare("INSERT INTO booking_seats (booking_id, seat_id) VALUES (?,?)");
    foreach($seat_ids as $sid) {
      $ins->bind_param('ii', $booking_id, $sid);
      $ins->execute();
    }

    // Insert payment
    $pm = $mysqli->prepare("INSERT INTO payments (booking_id, method, details_masked, amount, status) VALUES (?,?,?,?, 'PAID')");
    $pm->bind_param('issd', $booking_id, $method, $details_masked, $total);
    $pm->execute();

    // Confirm booking
    $mysqli->query("UPDATE bookings SET status='CONFIRMED' WHERE id=$booking_id");

    $mysqli->commit();
    unset($_SESSION['pending_booking']);
    redirect('/draft2/confirm.php?ref='.$ref);
  } catch (Throwable $e) {
    $mysqli->rollback();
    set_flash('error','Payment failed. Please try again.');
    redirect('/draft2/payment.php');
  }
}

// Load summary for display
$pb = $_SESSION['pending_booking'] ?? null;
if (!$pb) { set_flash('error','No pending booking.'); redirect('/draft2/now_showing.php'); }
$schedule_id = (int)$pb['schedule_id'];
$seat_ids = $pb['seat_ids'];
$sched = $mysqli->query("SELECT s.*, m.title, b.name AS branch_name FROM schedules s JOIN movies m ON m.id=s.movie_id JOIN branches b ON b.id=s.branch_id WHERE s.id=$schedule_id")->fetch_assoc();
$seat_labels = [];
if ($seat_ids) {
  $in = implode(',', array_map('intval',$seat_ids));
  $res = $mysqli->query("SELECT label FROM seats WHERE id IN ($in) ORDER BY row_char, col_number");
  while($r = $res->fetch_assoc()) $seat_labels[] = $r['label'];
}
$total = (float)$sched['price'] * count($seat_ids);
?>
<div class="container py-4">
  <h1 class="tc-heading mb-3">Payment</h1>
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h5 class="card-title">Choose Payment Method</h5>
          <form method="post" action="/draft2/payment.php">
            <input type="hidden" name="action" value="pay">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="method" id="m-gcash" value="GCASH" checked>
              <label class="form-check-label" for="m-gcash">GCash</label>
            </div>
            <div id="pay-gcash" class="mt-2">
              <label class="form-label">Registered Mobile Number</label>
              <input type="tel" name="gcash_mobile" class="form-control" placeholder="09XXXXXXXXX">
              <div class="form-text">A simulated verification will occur after submitting.</div>
            </div>
            <div class="form-check mt-3">
              <input class="form-check-input" type="radio" name="method" id="m-maya" value="MAYA">
              <label class="form-check-label" for="m-maya">Maya</label>
            </div>
            <div id="pay-maya" class="mt-2" style="display:none;">
              <label class="form-label">Registered Mobile Number</label>
              <input type="tel" name="maya_mobile" class="form-control" placeholder="09XXXXXXXXX">
              <div class="form-text">Simulated redirect and verification.</div>
            </div>
            <div class="form-check mt-3">
              <input class="form-check-input" type="radio" name="method" id="m-card" value="CARD">
              <label class="form-check-label" for="m-card">Debit / Credit Card</label>
            </div>
            <div id="pay-card" class="row g-3 mt-1" style="display:none;">
              <div class="col-md-6">
                <label class="form-label">Cardholder Name</label>
                <input type="text" name="card_name" class="form-control" placeholder="FULL NAME">
              </div>
              <div class="col-md-6">
                <label class="form-label">Card Number</label>
                <input type="text" name="card_number" class="form-control" placeholder="4111 1111 1111 1111">
              </div>
              <div class="col-md-4">
                <label class="form-label">Expiration (MM/YY)</label>
                <input type="text" name="card_exp" class="form-control" placeholder="MM/YY">
              </div>
              <div class="col-md-4">
                <label class="form-label">CVV</label>
                <input type="password" name="card_cvv" class="form-control" placeholder="123">
              </div>
            </div>
            <a class="btn btn-outline-light mt-3 me-2" href="/draft2/book.php?schedule_id=<?= (int)$schedule_id ?>">Cancel</a>
            <button class="btn btn-danger mt-3" type="submit">Pay <?=price_format($total)?></button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h5 class="card-title">Booking Summary</h5>
          <ul class="list-unstyled small text-secondary">
            <li>Movie: <span class="text-white"><?=esc($sched['title'])?></span></li>
            <li>Branch: <span class="text-white"><?=esc($sched['branch_name'])?></span></li>
            <li>Showtime: <span class="text-white"><?=date('M d, Y g:i A', strtotime($sched['show_datetime']))?></span></li>
            <li>Seats: <span class="text-white"><?=esc(implode(', ', $seat_labels))?></span></li>
            <li>Price per seat: <span class="text-white"><?=price_format($sched['price'])?></span></li>
            <li>Total: <span class="text-white"><?=price_format($total)?></span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="/draft2/assets/js/payment.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
