

document.addEventListener('DOMContentLoaded', () => {
    const modalEditar = document.getElementById('modalEditarLivro');

    const limparNumeros = (el) => el?.addEventListener('input', function () { 
        this.value = this.value.replace(/\D/g, ''); 
    });
    limparNumeros(document.getElementById('livro_edicao'));
    limparNumeros(document.getElementById('livro_anoPublicacao'));

    const formatarDinheiro = (el) => el?.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '');
        valor = (Number(valor) / 100).toFixed(2);
        if (valor === "0.00") {
            this.value = '';
        } else {
            this.value = 'R$ ' + valor
                .replace('.', ',')
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }
    });
    formatarDinheiro(document.getElementById('livro_valor'));

    if (modalEditar) {
        modalEditar.addEventListener('shown.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const codL = button.getAttribute('data-codl');
            const formEditar = modalEditar.querySelector('#formEditarLivro');
            
            if (formEditar) {
                formEditar.action = `/livro/${codL}/editar`;
            }

            const setVal = (id, attr) => {
                const el = modalEditar.querySelector(`#${id}`);
                if (el) el.value = button.getAttribute(attr) || '';
            };

            setVal('livro_titulo', 'data-titulo');
            setVal('livro_editora', 'data-editora');

            const inputEdicao = modalEditar.querySelector('#livro_edicao');
            limparNumeros(inputEdicao);
            setVal('livro_edicao', 'data-edicao');

            const inputAno = modalEditar.querySelector('#livro_anoPublicacao');
            limparNumeros(inputAno);
            setVal('livro_anoPublicacao', 'data-ano');
            
            const inputValor = modalEditar.querySelector('#livro_valor');
            formatarDinheiro(inputValor);

            const valorFloat = parseFloat(button.getAttribute('data-valor')) || 0;
            const valorFormatado = 'R$ ' + valorFloat.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            inputValor.value = valorFormatado;
            

            const autoresIds = button.getAttribute('data-autores')?.split(',').map(Number).filter(n => !isNaN(n)) || [];
            const assuntosIds = button.getAttribute('data-assuntos')?.split(',').map(Number).filter(n => !isNaN(n)) || [];
            
            const tsAutoresEl = modalEditar.querySelector('#livro_autores');
            const tsAssuntosEl = modalEditar.querySelector('#livro_assuntos');

            if (tsAutoresEl?.tomselect) tsAutoresEl.tomselect.setValue(autoresIds);
            if (tsAssuntosEl?.tomselect) tsAssuntosEl.tomselect.setValue(assuntosIds);
        });
    }
});

window.confirmarExclusao = function(event, titulo) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: `Deseja realmente excluir o livro "${titulo}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
    return false;
};