document.addEventListener("DOMContentLoaded", function() {
    function configurarFormularioComToast(formId, toastId) {
        const form = document.getElementById(formId);
        const toastElement = document.getElementById(toastId);
        
        // Só executa se encontrar o formulário e o toast na página atual
        if (form && toastElement) {
            form.addEventListener("submit", function(event) {
                // 1. Trava o envio imediato e o salto de página
                event.preventDefault(); 
                
                // 2. Inicializa e mostra o Toast de sucesso
                const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                toast.show();
                
                // 3. Aguarda 2 segundos para o utilizador ler a mensagem, e depois muda de página
                setTimeout(function() {
                    window.location.href = form.getAttribute('action') || 'lista.html';
                }, 2000);
            });
        }
    }
    
    // 1. Módulo de Equipamentos
    configurarFormularioComToast("formNovoEquipamento", "sucessoToast");
    
    // 2. Módulo de Fornecedores
    configurarFormularioComToast("formNovoFornecedor", "sucessoToastFornecedor");

    // 3. Módulo de Localizações 
    configurarFormularioComToast("formNovaLocalizacao", "sucessoToastLocalizacao");
    configurarFormularioComToast("formNovoDocumento", "sucessoToastDocumento");
});
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
