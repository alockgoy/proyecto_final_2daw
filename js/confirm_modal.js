// Función para crear y mostrar modal de confirmación  
function showConfirmModal(message, confirmUrl, confirmText = 'Si', cancelText = 'No') {  
    // Crear el modal  
    const modalHtml = `  
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-theme="dark">  
            <div class="modal-dialog">  
                <div class="modal-content">  
                    <div class="modal-header">  
                        <h5 class="modal-title text-light" id="confirmModalLabel">Confirmación</h5>  
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  
                    </div>  
                    <div class="modal-body text-light">  
                        ${message}  
                    </div>  
                    <div class="modal-footer">  
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelText}</button>  
                        <a href="${confirmUrl}" class="btn btn-danger">${confirmText}</a>  
                    </div>  
                </div>  
            </div>  
        </div>  
    `;  
      
    // Remover modal anterior si existe  
    const existingModal = document.getElementById('confirmModal');  
    if (existingModal) {  
        existingModal.remove();  
    }  
      
    // Añadir modal al body  
    document.body.insertAdjacentHTML('beforeend', modalHtml);  
      
    // Mostrar modal  
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));  
    modal.show();  
      
    // Limpiar modal cuando se cierre  
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function () {  
        this.remove();  
    });  
}  
  
// Event listener para enlaces con data-confirm  
document.addEventListener('DOMContentLoaded', function() {  
    document.addEventListener('click', function(e) {  
        const target = e.target.closest('[data-confirm]');  
        if (target) {  
            e.preventDefault();  
            const message = target.getAttribute('data-confirm');  
            const url = target.getAttribute('data-url') || target.href;  
            const confirmText = target.getAttribute('data-confirm-text') || 'Confirmar';  
            const cancelText = target.getAttribute('data-cancel-text') || 'Cancelar';  
              
            showConfirmModal(message, url, confirmText, cancelText);  
        }  
    });  
});