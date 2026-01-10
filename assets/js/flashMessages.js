let flashMessages = document.getElementById('flash-messages');
let flashContainer = document.getElementById('flash-container');
let progressBar = document.getElementById('progress-bar');

let duration = 5000;
let remaining = duration;
let startTime = null;
let timerId = null;
let rafId = null;

function startTimer() {
    startTime = Date.now();

    timerId = setTimeout(() => {
        flashContainer.style.right = -flashContainer.getBoundingClientRect().width + 'px';
        setTimeout(() => {
            flashContainer.style.display = 'none';
        }, 500);
    }, remaining);

    updateProgress();
}

function pauseTimer() {
    if (!timerId) return;

    clearTimeout(timerId);
    timerId = null;
    remaining -= Date.now() - startTime;

    cancelAnimationFrame(rafId);
}

function updateProgress() {
    let elapsed = duration - remaining + (Date.now() - startTime);
    let progress = Math.max(0, 1 - elapsed / duration);

    progressBar.style.width = `${progress * 100}%`;

    rafId = requestAnimationFrame(updateProgress);
}

if (flashMessages && flashMessages.innerHTML.trim() !== '') {
    startTimer();

    flashContainer.style.display = 'block';

    flashMessages.addEventListener('mouseenter', pauseTimer);
    flashMessages.addEventListener('mouseleave', startTimer);
}