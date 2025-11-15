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
    $cardRaw = post('card_number');
    $cardDigits = preg_replace('/\D+/', '', $cardRaw);
    $exp = strtoupper(trim(post('card_exp')));
    $cvv = preg_replace('/\D+/', '', post('card_cvv'));
    if (strlen($name) < 2) {
      set_flash('error','Enter cardholder name.');
      redirect('/draft2/payment.php');
    }
    if (!preg_match('/^\d{13,19}$/', $cardDigits)) {
      set_flash('error','Card number must be 13–19 digits.');
      redirect('/draft2/payment.php');
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $exp)) {
      set_flash('error','Expiration must be MM/YY.');
      redirect('/draft2/payment.php');
    }
    if (!preg_match('/^\d{3}$/', $cvv)) {
      set_flash('error','CVV must be exactly 3 digits.');
      redirect('/draft2/payment.php');
    }
    $details_masked = $name . ' • **** **** **** ' . substr($cardDigits, -4) . ' • ' . $exp;
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

            <!-- Dropdown instead of radio buttons -->
            <div class="mb-3">
              <label class="form-label">Payment Method</label>
              <select name="method" id="method-select" class="form-select">
                <option value="GCASH">GCash</option>
                <option value="MAYA">Maya</option>
                <option value="CARD">Debit / Credit Card</option>
              </select>
            </div>

            <div id="pay-gcash" class="mt-2">
              <label class="form-label">Registered Mobile Number</label>
              <input type="tel" name="gcash_mobile" class="form-control" placeholder="09XXXXXXXXX">
              <div class="form-text"></div>
            </div>

            <div id="pay-maya" class="mt-2" style="display:none;">
              <label class="form-label">Registered Mobile Number</label>
              <input type="tel" name="maya_mobile" class="form-control" placeholder="09XXXXXXXXX">
              <div class="form-text"></div>
            </div>

            <div id="pay-card" class="row g-3 mt-1" style="display:none;">
              <div class="col-md-6">
                <label class="form-label">Cardholder Name</label>
                <input type="text" name="card_name" class="form-control" placeholder="FULL NAME" autocomplete="cc-name">
              </div>
              <div class="col-md-6">
                <label class="form-label">Card Number</label>
                <input type="text"
                       name="card_number"
                       class="form-control"
                       placeholder="4111 1111 1111 1111 111"
                       inputmode="numeric"
                       autocomplete="cc-number"
                       maxlength="23"
                       pattern="[\d\s]{13,23}"
                       oninput="this.value=this.value.replace(/[^\d]/g,'').slice(0,19).replace(/(\d{4})(?=\d)/g,'$1 ').trim();">
              </div>
              <div class="col-md-4">
                <label class="form-label">Expiration (MM/YY)</label>
                <input type="text"
                       name="card_exp"
                       class="form-control"
                       placeholder="MM/YY"
                       inputmode="numeric"
                       autocomplete="cc-exp"
                       maxlength="5"
                       pattern="(0[1-9]|1[0-2])\/\d{2}"
                       oninput="let v=this.value.replace(/[^0-9]/g,''); if(v.length>4)v=v.slice(0,4); if(v.length>=3)v=v.slice(0,2)+'/'+v.slice(2); this.value=v;">
              </div>
              <div class="col-md-4">
                <label class="form-label">CVV</label>
                <input type="password"
                       name="card_cvv"
                       class="form-control"
                       placeholder="•••"
                       inputmode="numeric"
                       autocomplete="off"
                       maxlength="3"
                       pattern="\d{3}"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,3);"
                       aria-label="Card CVV (hidden)">
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
<script>
// Simple dropdown-based section toggle
(function(){
  const sel   = document.getElementById('method-select');
  const gcash = document.getElementById('pay-gcash');
  const maya  = document.getElementById('pay-maya');
  const card  = document.getElementById('pay-card');
  function update() {
    const v = sel.value;
    gcash.style.display = v === 'GCASH' ? 'block' : 'none';
    maya.style.display  = v === 'MAYA'  ? 'block' : 'none';
    card.style.display  = v === 'CARD'  ? 'flex' : 'none';
  }
  sel.addEventListener('change', update);
  update();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
