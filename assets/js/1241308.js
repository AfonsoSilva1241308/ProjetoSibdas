document.addEventListener("DOMContentLoaded", function() {
    
    
    const formEquipamento = document.getElementById("formNovoEquipamento");
    
    
    if (formEquipamento) {
        formEquipamento.addEventListener("submit", function(event) {
            
            
            event.preventDefault();
            
            
            const toastElement = document.getElementById("sucessoToast");
            
            
            const toast = new bootstrap.Toast(toastElement, {
                delay: 4000 
            });
            
            
            toast.show();
            
            
            formEquipamento.reset();
        });
    }
});