

document.addEventListener('DOMContentLoaded', () => {
    const modalEditar = document.getElementById('modalEditarAutor');
    const formEditar = document.getElementById('formEditarDinamico');
    const inputNome = document.getElementById('inputEditarNome');

    if (modalEditar && formEditar && inputNome) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const codAu = button.getAttribute('data-codau');
            const nome = button.getAttribute('data-nome');
            
            const novaAction = `/autor/${codAu}/editar`;
            formEditar.action = novaAction;

            inputNome.value = nome;
        });
    }
});

window.confirmarExclusao = function(event, nome) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: `Deseja realmente excluir o autor "${nome}"?`,
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