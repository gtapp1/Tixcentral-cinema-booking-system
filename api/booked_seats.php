<?php
// Seats only blocked for CONFIRMED; CANCELLED bookings free seats automatically.
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
$schedule_id = (int)($_GET['schedule_id'] ?? 0);
if ($schedule_id <= 0) { echo json_encode([]); exit; }
$q = $mysqli->query("SELECT bs.seat_id FROM booking_seats bs JOIN bookings b ON b.id=bs.booking_id WHERE b.schedule_id=$schedule_id AND b.status='CONFIRMED'");
$out = [];
while ($r = $q->fetch_assoc()) { $out[] = (int)$r['seat_id']; }
echo json_encode($out);
exit;
