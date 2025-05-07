document.addEventListener('DOMContentLoaded', function () {
    // Toggle for password field
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#pass');
    const iconPassword = togglePassword;

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon
        if (type === 'password') {
            iconPassword.classList.remove('fa-eye-slash');
            iconPassword.classList.add('fa-eye');
        } else {
            iconPassword.classList.remove('fa-eye');
            iconPassword.classList.add('fa-eye-slash');
        }
    });
});
