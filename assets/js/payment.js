(function(){
  const radios = document.querySelectorAll('input[name="method"]');
  const gcash = document.getElementById('pay-gcash');
  const maya = document.getElementById('pay-maya');
  const card = document.getElementById('pay-card');
  function update(){
    const v = document.querySelector('input[name="method"]:checked')?.value;
    gcash.style.display = v==='GCASH' ? 'block' : 'none';
    maya.style.display = v==='MAYA' ? 'block' : 'none';
    card.style.display = v==='CARD' ? 'block' : 'none';
  }
  radios.forEach(r=>r.addEventListener('change', update));
  update();
})();
