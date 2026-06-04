// Public JavaScript entrypoint.
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('[data-auth-modal]');

    if (!modal) {
        return;
    }

    const triggers = document.querySelectorAll('[data-auth-open]');
    const closers = modal.querySelectorAll('[data-auth-close]');
    const modalTitle = modal.querySelector('#auth-modal-title');

    const setModalTitle = (mode) => {
        if (!modalTitle) {
            return;
        }

        if (mode === 'register') {
            modalTitle.textContent = 'Create your Bolinao account';
        } else {
            modalTitle.textContent = 'Log in to your account';
        }
    };

    const openModal = (mode = 'signin') => {
        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        modal.classList.toggle('auth-modal-register', mode === 'register');
        setModalTitle(mode);

        const panes = modal.querySelectorAll('.auth-pane');
        panes.forEach((p) => {
            const paneMode = p.getAttribute('data-auth-pane');
            if (paneMode === mode) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });

        const firstInput = modal.querySelector('.auth-pane.active input');
        firstInput?.focus();
    };

    const closeModal = () => {
        modal.hidden = true;
        document.body.style.overflow = '';
        modal.classList.remove('auth-modal-register');

        const panes = modal.querySelectorAll('.auth-pane');
        panes.forEach((p) => p.classList.remove('active'));
        const signin = modal.querySelector('[data-auth-pane="signin"]');
        if (signin) signin.classList.add('active');
        setModalTitle('signin');
    };

    const openFromQuery = () => {
        const params = new URLSearchParams(window.location.search);
        const mode = params.get('auth');

        if (mode === 'signin' || mode === 'register') {
            openModal(mode);
            params.delete('auth');
            const url = `${window.location.pathname}${params.toString() ? `?${params.toString()}` : ''}`;
            window.history.replaceState({}, document.title, url);
        }
    };

    const openOnError = () => {
        const errorPanel = modal.querySelector('.auth-pane .alert-error');
        if (errorPanel) {
            const pane = errorPanel.closest('.auth-pane');
            const mode = pane?.getAttribute('data-auth-pane') || 'signin';
            openModal(mode);
        }
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const mode = trigger.dataset.authMode || trigger.dataset.authmode || 'signin';
            openModal(mode);
        });
    });

    closers.forEach((closer) => {
        closer.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    openFromQuery();
    openOnError();
});
