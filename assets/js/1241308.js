// ==========================================
// 1. FUNÇÕES GLOBAIS E FORMULÁRIOS
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    function configurarFormularioComToast(formId, toastId) {
        const form = document.getElementById(formId);
        const toastElement = document.getElementById(toastId);
        
        if (form && toastElement) {
            form.addEventListener("submit", function(event) {
                event.preventDefault(); 
                const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                toast.show();
                setTimeout(function() {
                    window.location.href = form.getAttribute('action') || 'lista.html';
                }, 2000);
            });
        }
    }
    
    configurarFormularioComToast("formNovoEquipamento", "sucessoToast");
    configurarFormularioComToast("formNovoFornecedor", "sucessoToastFornecedor");
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

// ==========================================
// 2. GRÁFICOS (DASHBOARD)
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    
    // Proteção: Só avança se a biblioteca Chart.js estiver carregada na página
    if (typeof Chart !== 'undefined') {
        
        const canvasFornecedores = document.getElementById('graficoFornecedores');
        if (canvasFornecedores) {
            new Chart(canvasFornecedores.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['GE Healthcare', 'Philips', 'Siemens Healthineers', 'Medtronic', 'Dräger'],
                    datasets: [{ label: 'Nº de Equipamentos', data: [210, 185, 142, 98, 65], backgroundColor: '#0d6efd', borderRadius: 4 }]
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { borderDash: [4, 4] } } } }
            });
        }

        const canvasIdade = document.getElementById('graficoIdade');
        if (canvasIdade) {
            new Chart(canvasIdade.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['< 2 Anos', '2 a 5 Anos', '5 a 10 Anos', ['> 10 Anos', '(Fim de Vida)']],
                    datasets: [{ label: 'Número de Equipamentos', data: [350, 480, 290, 125], backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'], borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { maxRotation: 0, minRotation: 0 } }, y: { beginAtZero: true, grid: { borderDash: [4, 4] } } } }
            });
        }

        const canvasServicos = document.getElementById('graficoServicos');
        if (canvasServicos) {
            new Chart(canvasServicos.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Urgência', 'Cuidados Intensivos', 'Bloco Operatório', 'Imagiologia', 'Internamento'],
                    datasets: [
                        { label: 'Nº Equipamentos', data: [145, 89, 120, 45, 210], backgroundColor: '#0d6efd', borderRadius: 4 },
                        { label: 'Suporte de Vida', data: [40, 65, 30, 2, 10], backgroundColor: '#dc3545', borderRadius: 4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } } }
            });
        }

        const canvasCriticidade = document.getElementById('graficoCriticidade');
        if (canvasCriticidade) {
            new Chart(canvasCriticidade.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Baixa: 450', 'Média: 320', 'Alta: 180', 'Suporte de Vida: 147'],
                    datasets: [{ data: [450, 320, 180, 147], backgroundColor: ['#198754', '#ffc107', '#fd7e14', '#dc3545'], borderWidth: 0, hoverOffset: 6 }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 20, font: { size: 13, family: "'Segoe UI', Roboto, Helvetica, Arial, sans-serif" } } } } }
            });
        }
    } else {
        console.warn("Chart.js não encontrado. Os gráficos não serão carregados.");
    }
});

// ==========================================
// 3. PÁGINA DE EDITAR / DETALHES
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // 3.1 Mostrar/Esconder Equipamento Pai
    const checkComponente = document.getElementById('checkComponente');
    const divPai = document.getElementById('divEquipamentoPai');
    if (checkComponente && divPai) {
        checkComponente.addEventListener('change', function() {
            divPai.style.display = this.checked ? 'block' : 'none';
        });
    }

    // 3.2 Alerta (Toast) ao Guardar Alterações
    const btnGuardar = document.querySelector('.btn-guardar');
    const toastEl = document.getElementById('toastGravacao');
    if (btnGuardar && toastEl) {
        const toast = new bootstrap.Toast(toastEl, { delay: 1500 });
        btnGuardar.addEventListener('click', function(e) {
            e.preventDefault(); 
            toast.show();
            setTimeout(() => { window.location.href = 'detalhes.html'; }, 1500);
        });
    }

    // 3.3 Remover Consumíveis (Ação com Modal)
    let linhaAtualConsumivel = null;
    document.querySelectorAll('.btn-abrir-modal-remover').forEach(botao => {
        botao.addEventListener('click', function() {
            linhaAtualConsumivel = this.closest('tr');
        });
    });

    const btnConfirmarRemocao = document.getElementById('btnConfirmarRemocaoConsumivel');
    if (btnConfirmarRemocao) {
        btnConfirmarRemocao.addEventListener('click', function() {
            if (linhaAtualConsumivel) {
                linhaAtualConsumivel.remove();
                const modalInstancia = bootstrap.Modal.getInstance(document.getElementById('modalRemoverConsumivel'));
                if (modalInstancia) modalInstancia.hide();
                linhaAtualConsumivel = null;
                
                const corpoConsumiveis = document.getElementById('corpoTabelaConsumiveis');
                if (corpoConsumiveis && corpoConsumiveis.children.length === 0) {
                    document.getElementById('contentorTabelaConsumiveis').classList.add('d-none');
                    document.getElementById('msgSemConsumiveis').classList.remove('d-none');
                }
            }
        });
    }

    // 3.4 Remover Documentos (Ação com Modal Dinâmica)
    let linhaAtualDoc = null;
    document.querySelectorAll('.btn-abrir-modal-remover-doc').forEach(botao => {
        botao.addEventListener('click', function() {
            linhaAtualDoc = this.closest('tr');
            const tipoDoc = linhaAtualDoc.querySelector('td:nth-child(1)').innerText.trim();
            const tagFicheiro = linhaAtualDoc.querySelector('td:nth-child(2) a');
            const nomeFicheiro = tagFicheiro ? tagFicheiro.innerText.trim() : 'Documento Desconhecido';
            
            const elementoTipoDoc = document.getElementById('tipoDocModal');
            const elementoNomeDoc = document.getElementById('nomeDocModal');
            if (elementoTipoDoc) elementoTipoDoc.innerText = tipoDoc;
            if (elementoNomeDoc) elementoNomeDoc.innerText = nomeFicheiro;
        });
    });

    const btnConfirmarRemocaoDoc = document.getElementById('btnConfirmarRemocaoDoc');
    if (btnConfirmarRemocaoDoc) {
        btnConfirmarRemocaoDoc.addEventListener('click', function() {
            if (linhaAtualDoc) {
                linhaAtualDoc.remove();
                const modalInstancia = bootstrap.Modal.getInstance(document.getElementById('modalRemoverDocumento'));
                if (modalInstancia) modalInstancia.hide();
                linhaAtualDoc = null;
                
                const corpoDocs = document.getElementById('corpoTabelaDocs');
                if (corpoDocs && corpoDocs.children.length === 0) {
                    document.getElementById('contentorTabelaDocs').classList.add('d-none');
                    document.getElementById('msgSemDocs').classList.remove('d-none');
                }
            }
        });
    }

    // 3.5 Adicionar Novo Documento à Tabela
    const btnGuardarNovoDoc = document.getElementById('btnGuardarNovoDoc');
    if (btnGuardarNovoDoc) {
        btnGuardarNovoDoc.addEventListener('click', function() {
            const categoria = document.getElementById('novoDocCategoria').value;
            const ficheiroInput = document.getElementById('novoDocFicheiro');
            const titulo = document.getElementById('novoDocTitulo').value;
            const validade = document.getElementById('novoDocValidade').value;

            if (!categoria || !titulo || !ficheiroInput.files[0]) {
                alert("Por favor, preencha o Título, a Categoria e anexe um Ficheiro.");
                return;
            }

            const nomeFicheiro = ficheiroInput.files[0].name;
            const validadeTexto = validade ? validade.split('-').reverse().join('-') : 'N/A';

            const novaLinha = document.createElement('tr');
            novaLinha.innerHTML = `
                <td class="py-3 px-3 border-0 fw-medium text-dark">${categoria}</td>
                <td class="py-3 border-0">
                    <span class="d-block fw-medium">${titulo}</span>
                    <a href="#" class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1 text-decoration-none shadow-sm mt-1">
                        <i class="fa-solid fa-file-pdf text-danger me-1"></i> ${nomeFicheiro}
                    </a>
                </td>
                <td class="py-3 border-0 text-muted small">${validadeTexto}</td>
                <td class="py-3 pe-3 border-0 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover-doc px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverDocumento">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            novaLinha.querySelector('.btn-abrir-modal-remover-doc').addEventListener('click', function() {
                linhaAtualDoc = this.closest('tr');
                document.getElementById('tipoDocModal').innerText = categoria;
                document.getElementById('nomeDocModal').innerText = nomeFicheiro;
            });

            document.getElementById('corpoTabelaDocs').appendChild(novaLinha);
            document.getElementById('contentorTabelaDocs').classList.remove('d-none');
            document.getElementById('msgSemDocs').classList.add('d-none');

            document.getElementById('formNovoDocInline').reset();
        });
    }
});