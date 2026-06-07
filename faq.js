document.addEventListener('DOMContentLoaded', function () {
    const triggers = document.querySelectorAll('.accordion-trigger');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
            const item = this.closest('.accordion-item');
            const isOpen = item.classList.contains('is-open');
            const body = item.querySelector('.accordion-body');

            if (isOpen) {
                item.classList.remove('is-open');
                this.setAttribute('aria-expanded', 'false');
                body.style.maxHeight = '0';
            } else {
                item.classList.add('is-open');
                this.setAttribute('aria-expanded', 'true');
                body.style.maxHeight = body.scrollHeight + 'px';
            }
        });
    });
});