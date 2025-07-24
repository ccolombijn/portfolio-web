export function navResponsive() {
    /**
     * Responsive
     */
    const mobileOpen = document.querySelector('.nav-mobile-open');
    const mobileClose = document.querySelector('.nav-mobile-close');
    const body = document.querySelector('body');
    mobileOpen.addEventListener('click', event => {
        body.classList.add('open')
    });
    mobileClose.addEventListener('click', event => {
        body.classList.remove('open');
    });
}