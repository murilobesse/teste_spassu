

document.addEventListener('DOMContentLoaded', () => {
    const modalEditar = document.getElementById('modalEditarAssunto');
    const formEditar = document.getElementById('formEditarDinamico');
    const inputDescricao = document.getElementById('inputEditarDescricao');

    if (modalEditar && formEditar && inputDescricao) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const codAs = button.getAttribute('data-codas');
            const descricao = button.getAttribute('data-descricao');
            
            const novaAction = `/assunto/${codAs}/editar`;
            formEditar.action = novaAction;

            inputDescricao.value = descricao;
        });
    }
});

window.confirmarExclusao = function(event, descricao) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: `Deseja realmente excluir o assunto "${descricao}"?`,
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