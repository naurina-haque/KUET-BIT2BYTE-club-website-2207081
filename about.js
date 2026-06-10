const revealItems = document.querySelectorAll('.reveal');
const carousels = document.querySelectorAll('[data-carousel]');
const memberCarousel = document.querySelector('[data-members-carousel]');

const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.2 }
);

revealItems.forEach((item) => observer.observe(item));

carousels.forEach((carousel) => {
    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;
    let activePointerId = null;
    let autoplayTimer = null;
    let snapTimer = null;

    const slideWidth = () => carousel.clientWidth;

    const stopAutoplay = () => {
        if (autoplayTimer !== null) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    };

    const startAutoplay = () => {
        stopAutoplay();
        autoplayTimer = window.setInterval(() => {
            if (isDragging) {
                return;
            }

            const width = slideWidth();
            if (!width) {
                return;
            }

            const totalSlides = carousel.children.length || 1;
            const currentIndex = Math.round(carousel.scrollLeft / width);
            const nextIndex = (currentIndex + 1) % totalSlides;
            carousel.scrollLeft = nextIndex * width;
        }, 1200);
    };

    const snapToNearestSlide = () => {
        const width = slideWidth();
        if (!width) {
            return;
        }

        const index = Math.round(carousel.scrollLeft / width);
        carousel.scrollLeft = index * width;
    };

    const stopDragging = () => {
        isDragging = false;
        carousel.classList.remove('is-dragging');
        activePointerId = null;
        snapToNearestSlide();
        startAutoplay();
    };

    const beginDrag = (clientX, pointerId = null) => {
        isDragging = true;
        activePointerId = pointerId;
        startX = clientX;
        startScrollLeft = carousel.scrollLeft;
        carousel.classList.add('is-dragging');
    };

    const moveDrag = (clientX) => {
        if (!isDragging) {
            return;
        }

        const distance = clientX - startX;
        carousel.scrollLeft = startScrollLeft - distance;
    };

    carousel.addEventListener('pointerdown', (event) => {
        beginDrag(event.clientX, event.pointerId);
        stopAutoplay();
    });

    carousel.addEventListener('pointermove', (event) => {
        if (!isDragging || (activePointerId !== null && event.pointerId !== activePointerId)) {
            return;
        }

        moveDrag(event.clientX);
    });

    carousel.addEventListener('pointerup', stopDragging);
    carousel.addEventListener('pointercancel', stopDragging);

    carousel.addEventListener('mousedown', (event) => {
        event.preventDefault();
        beginDrag(event.clientX);
        stopAutoplay();
    });

    document.addEventListener('mousemove', (event) => {
        if (isDragging && activePointerId === null) {
            moveDrag(event.clientX);
        }
    });

    document.addEventListener('mouseup', () => {
        if (isDragging && activePointerId === null) {
            stopDragging();
        }
    });

    carousel.addEventListener('touchstart', (event) => {
        const touch = event.touches[0];
        if (touch) {
            beginDrag(touch.clientX);
            stopAutoplay();
        }
    }, { passive: true });

    carousel.addEventListener('touchmove', (event) => {
        const touch = event.touches[0];
        if (isDragging && touch) {
            moveDrag(touch.clientX);
        }
    }, { passive: true });

    carousel.addEventListener('touchend', stopDragging);
    carousel.addEventListener('touchcancel', stopDragging);

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', () => {
        if (!isDragging) {
            startAutoplay();
        }
    });

    carousel.addEventListener('scroll', () => {
        if (isDragging) {
            return;
        }

        window.clearTimeout(snapTimer);
        snapTimer = window.setTimeout(snapToNearestSlide, 120);
    });

    startAutoplay();
});

