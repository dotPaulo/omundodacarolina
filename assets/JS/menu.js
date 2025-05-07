document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.querySelector(".hamburger");
    const navBar = document.querySelector(".navbar");
    document.addEventListener("click", (event) => {
        if (!event.target.closest(".navbar, .hamburger") && !isAutoSlide) {
            [navBar, hamburger].forEach(element => element.classList.remove("active"));
        }
    });

    hamburger.addEventListener("click", () => {
        [navBar, hamburger].forEach(element => element.classList.toggle("active"));
    });

    document.body.addEventListener("click", (event) => {
        if (!event.target.closest(".navbar, .hamburger") && hamburger.classList.contains("active")) {
            hamburger.classList.remove("active");
            navBar.classList.remove("active");
        }
    });
});
