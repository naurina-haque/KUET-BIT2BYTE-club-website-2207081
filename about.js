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
        }, 4500);
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

if (memberCarousel) {
    const track = memberCarousel.querySelector('[data-members-track]');
    const prevButton = memberCarousel.querySelector('[data-members-prev]');
    const nextButton = memberCarousel.querySelector('[data-members-next]');
    const originalCards = track ? Array.from(track.querySelectorAll('.member-card')) : [];
    let focusFrame = null;
    let isAdjustingLoop = false;
    let hasUserInteraction = false;
    

    if (track && originalCards.length > 1) {
        const firstClone = originalCards[0].cloneNode(true);
        const lastClone = originalCards[originalCards.length - 1].cloneNode(true);
        firstClone.dataset.clone = 'true';
        lastClone.dataset.clone = 'true';
        track.insertBefore(lastClone, originalCards[0]);
        track.appendChild(firstClone);
    }

    const cards = track ? Array.from(track.querySelectorAll('.member-card')) : [];

    const getStep = () => {
        if (!cards.length) {
            return 0;
        }

        const cardWidth = cards[0].getBoundingClientRect().width;
        const styles = window.getComputedStyle(track);
        const gapValue = parseFloat(styles.columnGap || styles.gap || '0') || 0;
        return cardWidth + gapValue;
    };

    const getVisibleIndex = () => {
        if (!track || !cards.length) {
            return 0;
        }

        const step = getStep();
        if (!step) {
            return 0;
        }

        return Math.round(track.scrollLeft / step);
    };

    const scrollToIndex = (index) => {
        if (!track || !cards.length) {
            return;
        }

        const step = getStep();
        if (!step) {
            return;
        }

        const total = Math.max(originalCards.length, 1);
        const targetIndex = ((index % total) + total) % total;
        track.scrollTo({
            left: targetIndex * step,
            behavior: 'smooth',
        });
    };

    const updateFocus = () => {
        if (!track || !cards.length) {
            return;
        }

        const trackRect = track.getBoundingClientRect();
        const centerX = trackRect.left + trackRect.width / 2;
        let closestCard = null;
        let closestDistance = Number.POSITIVE_INFINITY;

        cards.forEach((card) => {
            const cardRect = card.getBoundingClientRect();
            const cardCenter = cardRect.left + cardRect.width / 2;
            const distance = Math.abs(cardCenter - centerX);

            if (distance < closestDistance) {
                closestDistance = distance;
                closestCard = card;
            }
        });

        cards.forEach((card) => {
            card.classList.remove('is-center', 'is-side');
        });

        cards.forEach((card) => {
            const cardRect = card.getBoundingClientRect();
            const cardCenter = cardRect.left + cardRect.width / 2;
            const distanceRatio = Math.abs(cardCenter - centerX) / Math.max(trackRect.width / 2, 1);

            if (card === closestCard) {
                card.classList.add('is-center');
            } else if (distanceRatio < 1.2) {
                card.classList.add('is-side');
            }
        });
    };

    const normalizeLoopPosition = () => {
        if (!track || !cards.length || isAdjustingLoop || originalCards.length <= 1) {
    return;
}

        const step = getStep();
        if (!step) {
            return;
        }

        const currentIndex = Math.round(track.scrollLeft / step);

        if (currentIndex <= 0) {
            isAdjustingLoop = true;
            track.scrollTo({ left: (originalCards.length - 1) * step, behavior: 'auto' });
            isAdjustingLoop = false;
        } else if (currentIndex >= originalCards.length) {
            isAdjustingLoop = true;
            track.scrollTo({ left: step, behavior: 'auto' });
            isAdjustingLoop = false;
        }
    };

    const scheduleFocusUpdate = () => {
        if (focusFrame !== null) {
            return;
        }

        focusFrame = window.requestAnimationFrame(() => {
            focusFrame = null;
            normalizeLoopPosition();
            updateFocus();
        });
    };

    const scrollByStep = (direction) => {
        if (!track || !cards.length) {
            return;
        }

        scrollToIndex(getVisibleIndex() + direction);
    };

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            hasUserInteraction = true;
            scrollByStep(-1);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            hasUserInteraction = true;
            scrollByStep(1);
        });
    }

    if (track) {
       const initialPosition = () => {
       const step = getStep();
       track.scrollTo({ left: step, behavior: 'auto' });
       scheduleFocusUpdate();
    };

        track.addEventListener('pointerdown', () => {
            hasUserInteraction = true;
        });
        track.addEventListener('touchstart', () => {
            hasUserInteraction = true;
        }, { passive: true });
        track.addEventListener('mousedown', () => {
            hasUserInteraction = true;
        });

        track.addEventListener('scroll', scheduleFocusUpdate, { passive: true });
        window.addEventListener('resize', scheduleFocusUpdate, { passive: true });
        window.addEventListener('load', initialPosition);
        requestAnimationFrame(initialPosition);
    }
}
