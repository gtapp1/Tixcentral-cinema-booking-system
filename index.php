<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php
// Featured movie
$feat = $mysqli->query("SELECT * FROM movies WHERE featured=1 LIMIT 1")->fetch_assoc();
if (!$feat) { $feat = $mysqli->query("SELECT * FROM movies LIMIT 1")->fetch_assoc(); }
$movies = $mysqli->query("SELECT * FROM movies ORDER BY id DESC");
?>
<section class="hero" style="background-image:url('<?=esc($feat['hero_image_path'] ?: $feat['poster_path'])?>');">
  <div class="hero-content container">
    <h1 class="display-3 tc-title"><?=esc($feat['title'])?></h1>
    <p class="lead tagline"><?=esc($feat['synopsis'])?></p>
    <?php
      $sched = $mysqli->query("SELECT id FROM schedules WHERE movie_id=".$feat['id']." AND show_datetime >= NOW() ORDER BY show_datetime ASC LIMIT 1")->fetch_assoc();
      // Replace schedule-based booking URL with anchor to Now Showing movie card
      $bookUrl = "/draft2/now_showing.php#movie-".$feat['id'];
    ?>
    <a class="btn btn-danger btn-lg mt-2" href="<?=$bookUrl?>">Book Now</a>
  </div>
</section>

<div class="container my-5">
  <h2 class="tc-heading mb-3">Now Showing</h2>
  <div class="scroller">
    <?php while($m = $movies->fetch_assoc()): ?>
      <div class="movie-card">
        <div class="poster-wrap">
          <img class="movie-poster" src="<?=esc($m['poster_path'])?>" alt="<?=esc($m['title'])?>">
          <?php
            $s = $mysqli->query("SELECT id, DATE_FORMAT(show_datetime,'%b %d %I:%i %p') d FROM schedules WHERE movie_id=".$m['id']." AND show_datetime >= NOW() ORDER BY show_datetime ASC LIMIT 1")->fetch_assoc();
            $href = $s ? '/draft2/book.php?schedule_id='.$s['id'] : '/draft2/now_showing.php';
          ?>
          <div class="poster-overlay">
            <a class="btn btn-sm btn-danger" href="/draft2/now_showing.php#movie-<?=$m['id']?>">Book Now</a>
          </div>
        </div>
        <div class="p-3">
          <h5 class="movie-title mb-1"><?=esc($m['title'])?></h5>
          <div class="text-secondary small mb-2"><?=esc($m['genre'])?> • <?=esc($m['runtime_minutes'])?> min</div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
