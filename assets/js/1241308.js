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
// 3. Gráfico de Fornecedores (Barras Horizontais)
        // 3. Gráfico de Fornecedores (Barras Horizontais)
        const ctxFornecedores = document.getElementById('graficoFornecedores').getContext('2d');
        new Chart(ctxFornecedores, {
            type: 'bar',
            data: {
                labels: ['GE Healthcare', 'Philips', 'Siemens Healthineers', 'Medtronic', 'Dräger'],
                datasets: [{
                    label: 'Nº de Equipamentos',
                    data: [210, 185, 142, 98, 65],
                    backgroundColor: '#0d6efd', // O mesmo Azul exato do primeiro gráfico
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: { borderDash: [4, 4] } } }
            }
        });

       // 4. Gráfico de Idade do Parque (Barras Verticais Coloridas)
        const ctxIdade = document.getElementById('graficoIdade').getContext('2d');
        new Chart(ctxIdade, {
            type: 'bar',
            data: {
                // Ao colocar o último label entre parênteses retos, ele divide em duas linhas!
                labels: ['< 2 Anos', '2 a 5 Anos', '5 a 10 Anos', ['> 10 Anos', '(Fim de Vida)']],
                datasets: [{
                    label: 'Número de Equipamentos',
                    data: [350, 480, 290, 125],
                    backgroundColor: [
                        '#198754', // Verde 
                        '#0d6efd', // Azul 
                        '#ffc107', // Amarelo 
                        '#dc3545'  // Vermelho 
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { 
                        ticks: {
                            maxRotation: 0, // Impede o texto de rodar
                            minRotation: 0  // Força a ficar na horizontal
                        }
                    },
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [4, 4] } 
                    } 
                }
            }
        });
        // 1. Gráfico de Equipamentos por Serviço
    const canvasServicos = document.getElementById('graficoServicos');
    if (canvasServicos) {
        const ctxServicos = canvasServicos.getContext('2d');
        new Chart(ctxServicos, {
            type: 'bar',
            data: {
                labels: ['Urgência', 'Cuidados Intensivos', 'Bloco Operatório', 'Imagiologia', 'Internamento'],
                datasets: [{
                    label: 'Nº Equipamentos',
                    data: [145, 89, 120, 45, 210],
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                },
                {
                    label: 'Suporte de Vida',
                    data: [40, 65, 30, 2, 10],
                    backgroundColor: '#dc3545',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } }
            }
        });
    }

    // 2. Gráfico de Criticidade (Donut Premium)
    const canvasCriticidade = document.getElementById('graficoCriticidade');
    if (canvasCriticidade) {
        const ctxCriticidade = canvasCriticidade.getContext('2d');
        new Chart(ctxCriticidade, {
            type: 'doughnut',
            data: {
                labels: ['Baixa: 450', 'Média: 320', 'Alta: 180', 'Suporte de Vida: 147'],
                datasets: [{
                    data: [450, 320, 180, 147],
                    backgroundColor: ['#198754', '#ffc107', '#fd7e14', '#dc3545'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { 
                    legend: { 
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 13, family: "'Segoe UI', Roboto, Helvetica, Arial, sans-serif" }
                        }
                    } 
                }
            }
        });
    }
 document.addEventListener("DOMContentLoaded", function() {
    
    // Procura o Toast na página atual
    const toastElement = document.getElementById('toastGravacao');
    
    // Só avança se o Toast realmente existir nesta página HTML
    if (toastElement) {
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });

        const botoesGuardar = document.querySelectorAll('.btn-guardar');
        botoesGuardar.forEach(function(botao) {
            botao.addEventListener('click', function() {
                toast.show();
            });
        });
    }
    
    // Podes adicionar mais scripts globais do teu projeto abaixo desta linha
});   
document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Mostrar/Esconder Hierarquia
            const checkComponente = document.getElementById('checkComponente');
            const divPai = document.getElementById('divEquipamentoPai');
            if (checkComponente && divPai) {
                checkComponente.addEventListener('change', function() {
                    divPai.style.display = this.checked ? 'block' : 'none';
                });
            }

            // 2. Lógica do Toast e Redirecionamento
            const btnGuardar = document.querySelector('.btn-guardar');
            const toastEl = document.getElementById('toastGravacao');
            if(btnGuardar && toastEl) {
                const toast = new bootstrap.Toast(toastEl, { delay: 1500 });
                btnGuardar.addEventListener('click', function() {
                    toast.show();
                    setTimeout(() => { window.location.href = 'detalhes.html'; }, 1500);
                });
            }

            // 3. Remover Linhas Iniciais da Tabela de Documentos
            document.querySelectorAll('.btn-remover-linha').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('tr').remove();
                    verificarTabelaVazia();
                });
            });

            // Lógica partilhada com o JS global para adicionar novas linhas
            function verificarTabelaVazia() {
                const corpo = document.getElementById('corpoTabelaDocs');
                if (corpo && corpo.children.length === 0) {
                    document.getElementById('contentorTabelaDocs').classList.add('d-none');
                    document.getElementById('msgSemDocs').classList.remove('d-none');
                }
            }
        });