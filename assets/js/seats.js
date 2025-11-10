async function initSeatMap(scheduleId, price) {
  const container = document.getElementById('seat-map');
  const selectedInput = document.getElementById('selected-seats');
  const totalEl = document.getElementById('total-amount');
  const countEl = document.getElementById('seat-count');

  let booked = [];
  try {
    const res = await fetch(`/draft2/api/booked_seats.php?schedule_id=${encodeURIComponent(scheduleId)}`);
    booked = await res.json();
  } catch(e){ booked = []; }

  const items = container.querySelectorAll('.seat');
  items.forEach(item => {
    const id = parseInt(item.dataset.id, 10);
    if (booked.includes(id)) {
      item.classList.add('booked');
      item.setAttribute('aria-disabled','true');
    }
    item.addEventListener('click', () => {
      if (item.classList.contains('booked')) return;
      item.classList.toggle('selected');
      updateSelection();
    });
  });

  function updateSelection(){
    const selected = Array.from(container.querySelectorAll('.seat.selected'));
    const ids = selected.map(x => x.dataset.id);
    const count = ids.length;
    const total = (count * parseFloat(price || 0)).toFixed(2);
    selectedInput.value = ids.join(',');
    totalEl.textContent = `₱${total}`;
    countEl.textContent = `${count}`;
    document.getElementById('btn-payment').disabled = count === 0;
  }
  updateSelection();
}
