document.addEventListener("DOMContentLoaded", () => {
    let list = document.querySelector('.hero-slider .hero-list');
    let items = document.querySelectorAll('.hero-slider .hero-list .hero-item');
    let next = document.getElementById('next');
    let prev = document.getElementById('prev');
    let dots = document.querySelectorAll('.hero-slider .hero-dots li');

    let active = 0, lengthItems = items.length - 1, isAutoSlide = false, startX = 0, endX = 0;

    const reloadSlider = () => {
        let check = items[active].offsetLeft;
        list.style.left = -check + 'px';
        let lastActiveDot = document.querySelector('.hero-slider .hero-dots li.active');
        lastActiveDot.classList.remove('active');
        dots[active].classList.add('active');
        if (!isAutoSlide) {
            clearInterval(refreshInterval);
            refreshInterval = setInterval(nextSlide, 4000);
        }
    };

    const nextSlide = () => {
        isAutoSlide = true;
        active = active + 1 > lengthItems ? 0 : active + 1;
        reloadSlider();
        isAutoSlide = false;
    };

    const prevSlide = () => {
        isAutoSlide = true;
        active = active - 1 < 0 ? lengthItems : active - 1;
        reloadSlider();
        isAutoSlide = false;
    };

    next.onclick = nextSlide;
    prev.onclick = prevSlide;
    let refreshInterval = setInterval(nextSlide, 4000);
    dots.forEach((li, key) => {
        li.addEventListener('click', () => {
            active = key;
            reloadSlider();
        });
    });

    window.onresize = () => reloadSlider();
    list.addEventListener('touchstart', (e) => startX = e.touches[0].clientX);
    list.addEventListener('touchmove', (e) => endX = e.touches[0].clientX);
    list.addEventListener('touchend', (e) => {
        if (startX - endX > 50) nextSlide();
        else if (endX - startX > 50) prevSlide();
    });
});
