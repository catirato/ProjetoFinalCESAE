import './bootstrap';

//AQUI
// Alpine.js for interactive components
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// CSRF token setup for AJAX requests
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Utility functions
window.formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-PT');
};

window.showNotification = (message, type = 'success') => {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
};

// Form validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor preencha todos os campos obrigatórios', 'error');
            }
        });
    });
});

// Confirmation dialogs
window.confirmAction = (message) => {
    return confirm(message);
};

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
