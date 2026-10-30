/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// importar bootstrap
import 'bootstrap';

// importar sweetalert2
import Swal from 'sweetalert2';
window.Swal = Swal; // adiciona referencia global para o SweetAlert2

// importar tom-select
import 'tom-select/dist/css/tom-select.bootstrap5.css';
import TomSelect from 'tom-select';

// função para exibir toast de flash message do twig
document.addEventListener('DOMContentLoaded', () => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(el => {
        const type = el.dataset.type; // 'success' ou 'error'
        const message = el.textContent.trim();
        
        Toast.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: message
        });
    });

    // Inicializar Tom Select para selects múltiplos
    const selectsMultiplos = document.querySelectorAll('select.form-select[multiple]');
    selectsMultiplos.forEach(select => {
        new TomSelect(select, {
            plugins: ['remove_button'],
            placeholder: 'Selecionar...',
            maxItems: null
        });
    });
});

// Personalizar mensagem de validação nativa do HTML5 (Browser)
document.addEventListener('DOMContentLoaded', () => {
    const camposObrigatorios = document.querySelectorAll('input[required], select[required], textarea[required]');

    camposObrigatorios.forEach(campo => {
        campo.addEventListener('invalid', function () {
            if (this.validity.valueMissing) {
                this.setCustomValidity('Este campo é obrigatório!');
            }
        });
        
        campo.addEventListener('input', function () {
            this.setCustomValidity('');
        });
    });
});
