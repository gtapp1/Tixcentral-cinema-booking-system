<?php require_once __DIR__ . '/includes/header.php'; require_once __DIR__ . '/includes/auth.php'; require_login('/draft2/history.php'); ?>
<?php
// Handle cancel request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'cancel') {
  $uid = current_user()['id'];
  $bid = (int) post('booking_id', 0);
  $stmt = $mysqli->prepare("SELECT b.id, b.status, b.reference, s.show_datetime
    FROM bookings b
    JOIN schedules s ON s.id=b.schedule_id
    WHERE b.id=? AND b.user_id=? LIMIT 1");
  $stmt->bind_param('ii', $bid, $uid);
  $stmt->execute();
  $bk = $stmt->get_result()->fetch_assoc();
  if (!$bk) { set_flash('error','Booking not found.'); redirect('/draft2/history.php'); }
  if ($bk['status'] !== 'CONFIRMED') { set_flash('error','Only confirmed bookings can be cancelled.'); redirect('/draft2/history.php'); }
  if (strtotime($bk['show_datetime']) <= time()) { set_flash('error','Past or ongoing showtimes cannot be cancelled.'); redirect('/draft2/history.php'); }

  $mysqli->begin_transaction();
  try {
    $upd = $mysqli->prepare("UPDATE bookings SET status='CANCELLED' WHERE id=? AND status='CONFIRMED'");
    $upd->bind_param('i', $bid);
    $upd->execute();
    $mysqli->commit();
    // Redirect to structured page like confirmation, with flag
    redirect('/draft2/confirm.php?ref='.$bk['reference'].'&cancel=1');
  } catch (Throwable $e) {
    $mysqli->rollback();
    set_flash('error','Cancellation failed. Please try again.');
    redirect('/draft2/history.php');
  }
}
?>
<div class="container py-4">
  <h1 class="tc-heading mb-4">Your Bookings</h1>
  <?php
    $uid = current_user()['id'];
    $res = $mysqli->query("SELECT b.*, s.show_datetime, m.title, br.name branch_name
      FROM bookings b
      JOIN schedules s ON s.id=b.schedule_id
      JOIN movies m ON m.id=s.movie_id
      JOIN branches br ON br.id=s.branch_id
      WHERE b.user_id=$uid
      ORDER BY b.created_at DESC");
    if ($res->num_rows === 0): ?>
      <div class="alert alert-secondary">No bookings found.</div>
    <?php else: ?>
      <div class="list-group">
        <?php while($bk = $res->fetch_assoc()):
          $seats = $mysqli->query("SELECT s.label FROM booking_seats bs JOIN seats s ON s.id=bs.seat_id WHERE bs.booking_id=".$bk['id']." ORDER BY s.row_char, s.col_number");
          $seat_labels=[]; while($r=$seats->fetch_assoc()) $seat_labels[]=$r['label'];
          $pay = $mysqli->query("SELECT method, details_masked FROM payments WHERE booking_id=".$bk['id']." LIMIT 1")->fetch_assoc();
          $eligible = ($bk['status'] === 'CONFIRMED' && strtotime($bk['show_datetime']) > time());
        ?>
          <div class="list-group-item bg-dark text-light border-secondary">
            <div class="d-flex w-100 justify-content-between align-items-start">
              <div>
                <h5 class="mb-1 movie-title"><?=esc($bk['title'])?></h5>
                <p class="mb-1 text-secondary"><?=esc($bk['branch_name'])?> • <?=date('M d, Y g:i A', strtotime($bk['show_datetime']))?></p>
                <small class="text-secondary">Seats: <?=esc(implode(', ', $seat_labels))?> | Total: <?=price_format($bk['total_amount'])?> | Paid via: <?=esc($pay['method'] ?? '-')?></small>
                <div class="mt-1">
                  <a href="/draft2/confirm.php?ref=<?=$bk['reference']?>" class="btn btn-outline-light btn-sm">View</a>
                  <?php if ($eligible): ?>
                    <form method="post" action="/draft2/history.php" class="d-inline" onsubmit="return confirm('Cancel this booking? Seats will be released and a refund will be initiated.');">
                      <input type="hidden" name="action" value="cancel">
                      <input type="hidden" name="booking_id" value="<?=$bk['id']?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm">Cancel Booking</button>
                    </form>
                  <?php else: ?>
                    <span class="badge bg-secondary align-middle"><?=esc($bk['status'])?></span>
                  <?php endif; ?>
                </div>
              </div>
              <small class="text-secondary"><?=esc($bk['status'])?></small>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
