// Alpine.js components for Your Mind Aid

// Modal component
document.addEventListener('alpine:init', () => {
    Alpine.data('modal', (initialOpen = false) => ({
        open: initialOpen,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    }));

    // Dropdown component
    Alpine.data('dropdown', (initialOpen = false) => ({
        open: initialOpen,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    }));

    // Sidebar component
    Alpine.data('sidebar', (initialOpen = false) => ({
        open: initialOpen,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    }));
});
