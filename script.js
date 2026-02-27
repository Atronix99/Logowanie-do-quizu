document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            this.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

const container = document.querySelector('.container');
const SignUpLink = document.querySelector('.SignUpLink');
const SignInLink = document.querySelector('.SignInLink');

SignUpLink.addEventListener('click', () => {
    container.classList.add('active');
});

SignInLink.addEventListener('click', () => {
    container.classList.remove('active');
});

const popup = document.getElementById('popup-msg');
if (popup) {
    setTimeout(() => {
        popup.classList.add('hidden');
    }, 3000);
}