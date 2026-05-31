function toggleUserMenu() {
    const wrapper = document.getElementById('userDropdownWrapper');
    wrapper.classList.toggle('open');
}

// Cerrar al hacer clic fuera
document.addEventListener('click', function (e) {
    const wrapper = document.getElementById('userDropdownWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        wrapper.classList.remove('open');
    }
});

// Cerrar con Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const wrapper = document.getElementById('userDropdownWrapper');
        if (wrapper) wrapper.classList.remove('open');
    }
});
