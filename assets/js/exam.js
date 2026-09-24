document.addEventListener('DOMContentLoaded', function () {
  var timerEl = document.getElementById('timer-display');
  var examForm = document.getElementById('exam-form');
  if (!timerEl || !examForm) return;

  var remaining = parseInt(timerEl.dataset.seconds, 10);

  function formatTime(sec) {
    var m = Math.floor(sec / 60).toString().padStart(2, '0');
    var s = (sec % 60).toString().padStart(2, '0');
    return m + ':' + s;
  }

  function tick() {
    if (remaining <= 0) {
      timerEl.textContent = '00:00';
      clearInterval(interval);
      examSubmitting = true;
      examForm.submit();
      return;
    }
    remaining--;
    timerEl.textContent = formatTime(remaining);
    if (remaining <= 60) {
      timerEl.classList.add('warning');
    }
  }

  var examSubmitting = false;
  timerEl.textContent = formatTime(remaining);
  var interval = setInterval(tick, 1000);

  examForm.addEventListener('submit', function () {
    examSubmitting = true;
    clearInterval(interval);
  });

  window.addEventListener('beforeunload', function (e) {
    if (examSubmitting) return;
    e.preventDefault();
    e.returnValue = '';
  });
});
