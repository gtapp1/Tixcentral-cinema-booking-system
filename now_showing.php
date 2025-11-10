<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="container-fluid py-4">
  <h1 class="tc-heading mb-4">Now Showing</h1>
  <div class="row g-4">
    <?php
      $res = $mysqli->query("SELECT * FROM movies ORDER BY title ASC");
      while($m = $res->fetch_assoc()):
    ?>
      <div class="col-12 col-md-6 col-lg-3" id="movie-<?=$m['id']?>">
        <div class="card bg-dark border-secondary h-100 ns-card movie-card position-relative">
          <img src="<?=esc($m['poster_path'])?>" class="card-img-top poster-fit" alt="<?=esc($m['title'])?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title movie-title"><?=esc($m['title'])?></h5>
            <button type="button"
                    class="btn btn-outline-light synopsis-toggle mb-2"
                    data-target="syn-<?=$m['id']?>">Synopsis</button>
            <div class="mt-auto">
              <div class="small text-secondary mb-2"><?=esc($m['genre'])?> • <?=esc($m['runtime_minutes'])?> min</div>
              <?php
                // Fetch all schedules then group by branch
                $schedRes = $mysqli->query("SELECT s.id, b.name AS branch, DATE_FORMAT(s.show_datetime,'%b %d %I:%i %p') dt
                  FROM schedules s 
                  JOIN branches b ON b.id=s.branch_id
                  WHERE s.movie_id=".$m['id']." AND s.show_datetime >= NOW()
                  ORDER BY b.name, s.show_datetime ASC");
                $byBranch = [];
                while($row = $schedRes->fetch_assoc()){
                  $byBranch[$row['branch']][] = $row;
                }
              ?>
              <?php if (empty($byBranch)): ?>
                <span class="text-secondary small">No upcoming schedules.</span>
              <?php else: ?>
                <!-- Branch buttons -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <?php foreach($byBranch as $branchName => $items):
                    $slug = strtolower(preg_replace('/[^a-z0-9]+/i','-', $branchName));
                    $overlayId = 'branch-'.$m['id'].'-'.$slug;
                  ?>
                    <button type="button"
                            class="btn btn-outline-danger btn-sm branch-toggle"
                            data-target="<?=$overlayId?>"><?=esc($branchName)?></button>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- Synopsis overlay (unchanged) -->
          <div class="synopsis-overlay" id="syn-<?=$m['id']?>">
            <div class="synopsis-content">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="movie-title mb-0"><?=esc($m['title'])?></h6>
              </div>
              <p class="small"><?=esc($m['synopsis'])?></p>
            </div>
          </div>
          <!-- Branch showtimes overlays -->
          <?php foreach($byBranch as $branchName => $items):
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i','-', $branchName));
            $overlayId = 'branch-'.$m['id'].'-'.$slug;
          ?>
            <div class="synopsis-overlay branch-overlay" id="<?=$overlayId?>">
              <div class="synopsis-content">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="movie-title mb-0"><?=esc($m['title'])?> — <?=esc($branchName)?></h6>
                </div>
                <div class="row row-cols-2 g-2">
                  <?php foreach($items as $s): ?>
                    <div class="col">
                      <a class="btn btn-sm btn-outline-light w-100"
                         href="/draft2/book.php?schedule_id=<?=$s['id']?>"><?=esc($s['dt'])?></a>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<script>
// Highlight + scroll if hash matches a movie id
document.addEventListener('DOMContentLoaded', () => {
  const h = location.hash;
  if (h && h.startsWith('#movie-')) {
    const el = document.querySelector(h);
    if (el) {
      el.scrollIntoView({behavior:'smooth', block:'center'});
      const card = el.querySelector('.movie-card');
      if (card) {
        card.classList.add('highlight-movie');
        setTimeout(()=>card.classList.remove('highlight-movie'), 3000);
      }
    }
  }

  // Synopsis hover
  document.querySelectorAll('.synopsis-toggle').forEach(btn => {
    const id = btn.dataset.target;
    const ov = document.getElementById(id);
    if (!ov) return;
    btn.addEventListener('mouseenter', ()=> ov.classList.add('active'));
    btn.addEventListener('mouseleave', ()=> {
      setTimeout(()=> {
        if (!btn.matches(':hover') && !ov.matches(':hover')) ov.classList.remove('active');
      },150);
    });
    ov.addEventListener('mouseleave', ()=> {
      setTimeout(()=> { if (!btn.matches(':hover')) ov.classList.remove('active'); },150);
    });
  });

  // Branch button click -> show overlay, hide others inside same card
  document.querySelectorAll('.branch-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.target;
      const card = btn.closest('.movie-card');
      card.querySelectorAll('.branch-overlay.active').forEach(o => {
        if (o.id !== target) o.classList.remove('active');
      });
      const ov = document.getElementById(target);
      ov && ov.classList.toggle('active');
    });
  });

  // Auto close branch overlay when mouse leaves overlay & its trigger
  document.querySelectorAll('.branch-overlay').forEach(ov => {
    ov.addEventListener('mouseleave', () => {
      const btn = document.querySelector('.branch-toggle[data-target="'+ov.id+'"]');
      setTimeout(()=> {
        if (btn && !btn.matches(':hover') && !ov.matches(':hover')) ov.classList.remove('active');
      },150);
    });
  });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
