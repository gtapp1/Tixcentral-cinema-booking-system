<?php require_once __DIR__ . '/includes/header.php'; require_once __DIR__ . '/includes/auth.php'; require_login('/draft2/confirm.php'.(isset($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'')); ?>
<?php
$ref = getv('ref');
if (!$ref) { set_flash('error','Invalid reference.'); redirect('/draft2/'); }
$stmt = $mysqli->prepare("SELECT b.*, s.show_datetime, s.price, m.title, b2.name AS branch_name, p.method, p.details_masked
  FROM bookings b
  JOIN schedules s ON s.id=b.schedule_id
  JOIN movies m ON m.id=s.movie_id
  JOIN branches b2 ON b2.id=s.branch_id
  LEFT JOIN payments p ON p.booking_id=b.id
  WHERE b.reference=? AND b.user_id=?");
$uid = current_user()['id'];
$stmt->bind_param('si', $ref, $uid); $stmt->execute(); $booking = $stmt->get_result()->fetch_assoc();
if (!$booking) { set_flash('error','Reference not found.'); redirect('/draft2/'); }
$seats = $mysqli->query("SELECT label FROM booking_seats bs JOIN seats s ON s.id=bs.seat_id WHERE bs.booking_id=".$booking['id']." ORDER BY s.row_char, s.col_number");
$seat_labels=[]; while($r=$seats->fetch_assoc()) $seat_labels[]=$r['label'];
$status = $booking['status'];
$isCancelled = ($status === 'CANCELLED');
?>
<div class="container py-5 text-center">
  <div class="mb-3">
    <?php if ($isCancelled): ?>
      <span class="badge bg-danger">Booking Cancelled</span>
    <?php elseif ($status === 'CONFIRMED'): ?>
      <span class="badge bg-success">Payment Successful</span>
    <?php else: ?>
      <span class="badge bg-secondary"><?=esc($status)?></span>
    <?php endif; ?>
  </div>
  <h1 class="tc-heading"><?= $isCancelled ? 'Booking Cancelled' : 'Booking Confirmed' ?></h1>
  <?php if ($isCancelled): ?>
    <p class="text-secondary">Your booking has been cancelled. Refund will be processed within 3–5 business days.</p>
  <?php else: ?>
    <p class="text-secondary">Thank you! Your tickets have been booked.</p>
  <?php endif; ?>
  <div class="card bg-dark border-secondary mx-auto" style="max-width:700px;">
    <div class="card-body text-start">
      <div class="row">
        <div class="col-md-6">
          <div><strong>Reference:</strong> <?=$booking['reference']?></div>
          <div><strong>Movie:</strong> <?=esc($booking['title'])?></div>
          <div><strong>Branch:</strong> <?=esc($booking['branch_name'])?></div>
          <div><strong>Showtime:</strong> <?=date('M d, Y g:i A', strtotime($booking['show_datetime']))?></div>
        </div>
        <div class="col-md-6">
          <div><strong>Seats:</strong> <?=esc(implode(', ', $seat_labels))?></div>
          <div><strong>Status:</strong> <?=esc($status)?></div>
          <div><strong>Payment:</strong> <?=esc($booking['method'])?></div>
          <div><strong>Details:</strong> <?=esc($booking['details_masked'])?></div>
          <div><strong>Total:</strong> <?=price_format($booking['total_amount'])?></div>
        </div>
      </div>
      <?php if ($isCancelled): ?>
        <div class="mt-3 small text-danger">Seats released. You may rebook another schedule.</div>
      <?php endif; ?>
    </div>
  </div>
  <a class="btn btn-danger mt-4" href="/draft2/history.php">Back to Booking History</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
