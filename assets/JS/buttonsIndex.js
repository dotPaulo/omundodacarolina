document.addEventListener("DOMContentLoaded", function () {
  const contactArea = document.getElementById("contactClickArea");

  if (contactArea) {
    contactArea.addEventListener("click", function (event) {
      event.preventDefault();
      smoothScroll('footer', 4000, highlightEmailIcon);
    });
  }

  // New event listener for "Ler mais" button
  document.getElementById('ReadMore').addEventListener('click', function () {
    smoothScroll('projetos', 2000);
  });

  function smoothScroll(targetId, duration, callback) {
    const target = document.getElementById(targetId);
    const targetOffset = target.offsetTop;
    const startPosition = window.pageYOffset;
    const distance = targetOffset - startPosition;
    let start = null;

    function step(timestamp) {
      if (!start) start = timestamp;
      const progress = timestamp - start;
      window.scrollTo(0, easeInOut(progress, startPosition, distance, duration));
      if (progress < duration) {
        window.requestAnimationFrame(step);
      } else if (callback) {
        callback();
      }
    }
    window.requestAnimationFrame(step);
  }

  function highlightEmailIcon() {
    const emailIcon = document.querySelector('.footer-social a[href^="mailto:"] svg');
    const emailLink = emailIcon.closest('a');
    const messageElement = document.createElement('span');
    messageElement.textContent = "Envie um email aqui";
    messageElement.className = 'email-highlight-message';
    emailIcon.classList.add('highlight');
    emailLink.parentNode.insertBefore(messageElement, emailLink.nextSibling);
    setTimeout(() => {
      emailIcon.classList.add('fade-out');
      messageElement.classList.add('fade-out');

      setTimeout(() => {
        emailIcon.classList.remove('highlight', 'fade-out');
        messageElement.remove();
      }, 1000);
    }, 4000);
  }
});

function easeInOut(t, b, c, d) {
  t /= d / 2;
  if (t < 1) return c / 2 * t * t + b;
  t--;
  return -c / 2 * (t * (t - 2) - 1) + b;
}