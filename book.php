<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php
$schedule_id = (int) getv('schedule_id', 0);
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $schedule_id = (int) post('schedule_id', 0);
}
$movie_id = (int) getv('movie_id', 0);

function fetch_schedule($id, $mysqli) {
  $stmt = $mysqli->prepare("SELECT s.*, m.title, m.poster_path, m.genre, m.runtime_minutes, b.name AS branch_name 
    FROM schedules s JOIN movies m ON m.id=s.movie_id JOIN branches b ON b.id=s.branch_id WHERE s.id=?");
  $stmt->bind_param('i', $id); $stmt->execute(); return $stmt->get_result()->fetch_assoc();
}

if ($schedule_id > 0) {
  $sched = fetch_schedule($schedule_id, $mysqli);
  if (!$sched) { set_flash('error','Schedule not found'); redirect('/draft2/now_showing.php'); }
}
?>
<div class="container py-4">
<?php if (!$schedule_id): ?>
  <h1 class="tc-heading mb-4">Book Tickets</h1>
  <form method="get" action="/draft2/book.php" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Movie</label>
      <select name="movie_id" class="form-select" required onchange="this.form.submit()">
        <option value="">Select a movie</option>
        <?php
          $movies = $mysqli->query("SELECT id, title FROM movies ORDER BY title ASC");
          while($m = $movies->fetch_assoc()):
        ?>
          <option value="<?=$m['id']?>" <?=$movie_id===$m['id']?'selected':''?>><?=esc($m['title'])?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <?php if ($movie_id): ?>
    <div class="col-md-4">
      <label class="form-label">Branch</label>
      <select name="branch_id" class="form-select" required>
        <option value="">Select a branch</option>
        <?php
          $branches = $mysqli->query("SELECT DISTINCT b.id, b.name FROM schedules s JOIN branches b ON b.id=s.branch_id WHERE s.movie_id=$movie_id AND s.show_datetime >= NOW()");
          while($b = $branches->fetch_assoc()):
        ?>
          <option value="<?=$b['id']?>"><?=esc($b['name'])?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Showtime</label>
      <select name="schedule_id" class="form-select" required>
        <option value="">Select date & time</option>
        <?php
          $showtimes = $mysqli->query("SELECT s.id, DATE_FORMAT(s.show_datetime,'%b %d %I:%i %p') dt, b.name bn
            FROM schedules s JOIN branches b ON b.id=s.branch_id
            WHERE s.movie_id=$movie_id AND s.show_datetime >= NOW() ORDER BY s.show_datetime ASC");
          while($s = $showtimes->fetch_assoc()):
        ?>
          <option value="<?=$s['id']?>"><?=$s['dt']?> — <?=esc($s['bn'])?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-12">
      <button class="btn btn-danger">Continue</button>
    </div>
    <?php endif; ?>
  </form>
<?php else: ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="d-flex gap-3 align-items-center mb-3">
        <img src="<?=esc($sched['poster_path'])?>" alt="" style="width:80px; height:120px; object-fit:cover; border-radius:6px;">
        <div>
          <h2 class="movie-title mb-0"><?=esc($sched['title'])?></h2>
          <div class="text-secondary small"><?=esc($sched['branch_name'])?> • <?=date('M d, Y g:i A', strtotime($sched['show_datetime']))?> • <?=esc($sched['genre'])?> • <?=esc($sched['runtime_minutes'])?> min</div>
          <div class="mt-1">Price per seat: <strong><?=price_format($sched['price'])?></strong></div>
        </div>
      </div>

      <!-- START: Cinema-styled wrap -->
      <div class="cinema-wrap mb-3">
        <div class="screen">SCREEN</div>
        <div class="seat-area">
          <!-- Vertical aisle red lines (kept) -->
          <div class="cinema-aisle" style="left: calc((100% / 12) * 4);"></div>
          <div class="cinema-aisle" style="left: calc((100% / 12) * 8);"></div>

          <!-- NEW: precise row-light overlay -->
          <div class="row-lights" id="row-lights"></div>

          <div id="seat-map" class="seat-map">
            <?php
              $seats = $mysqli->query("SELECT * FROM seats ORDER BY row_char, col_number");
              while($seat = $seats->fetch_assoc()):
            ?>
              <div class="seat" data-id="<?=$seat['id']?>" title="<?=$seat['label']?>"><?=esc($seat['label'])?></div>
            <?php endwhile; ?>
          </div>
        </div>
        <div class="cinema-rail"></div>
      </div>
      <!-- END: Cinema-styled wrap -->

      <div class="seat-legend mt-3">
        <span class="legend-available"></span> Available
        <span class="legend-selected ms-3"></span> Selected
        <span class="legend-booked ms-3"></span> Booked
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h5 class="card-title tc-heading">Booking Summary</h5>
          <ul class="list-unstyled small text-secondary">
            <li>Movie: <span class="text-white"><?=esc($sched['title'])?></span></li>
            <li>Branch: <span class="text-white"><?=esc($sched['branch_name'])?></span></li>
            <li>Showtime: <span class="text-white"><?=date('M d, Y g:i A', strtotime($sched['show_datetime']))?></span></li>
            <li>Seats Selected: <span id="seat-count" class="text-white">0</span></li>
            <li>Total: <span id="total-amount" class="text-white"><?=price_format(0)?></span></li>
          </ul>
          <form method="post" action="/draft2/payment.php">
            <input type="hidden" name="schedule_id" value="<?=$sched['id']?>">
            <input type="hidden" name="selected_seats" id="selected-seats" value="">
            <button id="btn-payment" class="btn btn-danger w-100" type="submit" disabled>Proceed to Payment</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Inline styles for cinema accents -->
  <style>
    .cinema-wrap {
      position: relative;
      padding: 1rem 1rem 1.25rem;
      background: #0a0a0a;
      border-radius: 10px;
      border: 1px solid #1b1b1b;
      box-shadow:
        inset 0 0 0 2px rgba(229,9,20,.12),
        0 0 24px rgba(0,0,0,.6);
    }
    .cinema-wrap .screen {
      border-bottom-color: #e50914 !important;
      box-shadow: 0 4px 14px rgba(229,9,20,.35);
      margin-bottom: .75rem;
      padding-bottom: .25rem;
      letter-spacing: 2px;
    }
    .seat-area { position: relative; }

    /* Row lights overlay behind seats (exact per row) */
    .row-lights { position: absolute; inset: 0; pointer-events: none; z-index: 1; }
    .row-light {
      position: absolute; left: 0; right: 0; height: 2px;
      background: #e50914;
      box-shadow: 0 0 8px rgba(229,9,20,.6), 0 0 16px rgba(229,9,20,.35);
      opacity: .9;
    }
    .row-label {
      position: absolute;
      left: -6px; transform: translateX(-100%);
      background: #e50914; color: #fff;
      padding: 0 .3rem; font-size: .7rem; border-radius: 3px;
      box-shadow: 0 0 6px rgba(229,9,20,.6);
      letter-spacing: 1px; line-height: 1.3;
      pointer-events: none;
    }

    .cinema-aisle {
      position: absolute;
      top: 0.35rem;
      bottom: 0.35rem;
      width: 2px;
      background: #e50914;
      opacity: .55;
      filter: drop-shadow(0 0 6px rgba(229,9,20,.5));
      pointer-events: none;
      z-index: 1;
    }
    /* thin floor rail below seats */
    .cinema-rail {
      position: absolute;
      left: 8px; right: 8px; bottom: .5rem;
      height: 2px;
      background: linear-gradient(90deg, transparent, rgba(229,9,20,.7), transparent);
      filter: drop-shadow(0 0 4px rgba(229,9,20,.5));
      pointer-events: none;
    }
    /* Ensure seats are above aisles visually */
    #seat-map { position: relative; z-index: 2; }
  </style>

  <script src="/draft2/assets/js/seats.js"></script>
  <script>
    initSeatMap(<?= (int)$sched['id']?>, "<?= esc($sched['price']) ?>");

    // Draw precise red row lights based on actual seat row positions
    function renderRowLights() {
      const seatMap = document.getElementById('seat-map');
      const overlay = document.getElementById('row-lights');
      if (!seatMap || !overlay) return;
      overlay.innerHTML = '';

      const seats = Array.from(seatMap.querySelectorAll('.seat'));
      if (seats.length === 0) return;

      // Collect unique row letters from seat title (e.g., A1 => 'A')
      const rows = Array.from(new Set(seats.map(s => (s.getAttribute('title') || s.textContent || '')[0]))).sort();

      const mapRect = seatMap.getBoundingClientRect();
      rows.forEach(row => {
        const rowSeats = seats.filter(s => {
          const label = (s.getAttribute('title') || s.textContent || '');
          return label.startsWith(row);
        });
        if (rowSeats.length === 0) return;

        // Y position at vertical center of the first seat of the row
        const r0 = rowSeats[0].getBoundingClientRect();
        const y = (r0.top - mapRect.top) + (rowSeats[0].offsetHeight / 2);

        const line = document.createElement('div');
        line.className = 'row-light';
        line.style.top = y + 'px';
        overlay.appendChild(line);

        const label = document.createElement('div');
        label.className = 'row-label';
        label.style.top = (y - 10) + 'px';
        label.textContent = row;
        overlay.appendChild(label);
      });
    }

    window.addEventListener('resize', renderRowLights);
    window.addEventListener('load', renderRowLights);
    // Recalc shortly after layout to ensure fonts/images are positioned
    setTimeout(renderRowLights, 0);
  </script>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
