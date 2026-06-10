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
    
 const checkEComponente = document.getElementById('checkEComponente');
    const blocoPai = document.getElementById('blocoEquipamentoPai');
    const blocoFilhos = document.getElementById('blocoGerirFilhos'); // O contentor da tabela e botão vincular
    
    if (checkEComponente && blocoPai && blocoFilhos) {
        checkEComponente.addEventListener('change', function() {
            if (this.checked) {
                // É um COMPONENTE: Mostra o dropdown do Pai, Esconde a tabela de filhos
                blocoPai.classList.remove('d-none');
                blocoFilhos.classList.add('d-none');
            } else {
                // É EQUIPAMENTO PRINCIPAL: Esconde o dropdown do Pai, Mostra a tabela de filhos
                blocoPai.classList.add('d-none');
                blocoFilhos.classList.remove('d-none');
            }
        });
    }
    // --------------------------------------------------------
    // 3.1 Gestão de Hierarquia (Vincular/Desvincular Componentes COM MODAL)
    // --------------------------------------------------------

    // 1. Função preparatória para a Modal
    let linhaComponenteAtual = null;

    function ligarBotaoDesvincular(botao) {
        botao.addEventListener('click', function() {
            linhaComponenteAtual = this.closest('tr');
            // Vai buscar o nome do equipamento à 2ª coluna
            const nomeComponente = linhaComponenteAtual.querySelector('td:nth-child(2)').innerText;
            const modalTexto = document.getElementById('textoComponenteModal');
            if(modalTexto) modalTexto.innerText = nomeComponente;
        });
    }

    // 2. Função para verificar se a tabela ficou vazia e mostrar a mensagem
    function verificarTabelaComponentes() {
        const corpo = document.getElementById('corpoTabelaComp');
        if (corpo && corpo.children.length === 0) {
            document.getElementById('contentorTabelaComp').classList.add('d-none');
            document.getElementById('msgSemComp').classList.remove('d-none');
        }
    }

    // 3. Vincular Novo Componente (Adicionar à Tabela)
    const btnVincularComponente = document.getElementById('btnVincularComponente');
    if (btnVincularComponente) {
        btnVincularComponente.addEventListener('click', function() {
            const selectComp = document.getElementById('novoComponenteSelect');
            if (!selectComp.value) {
                alert("Por favor, selecione um equipamento da lista.");
                return;
            }

            const dados = selectComp.value.split('|');
            const designacao = dados[0];
            const codigo = dados[1];

            const novaLinha = document.createElement('tr');
            // Botão atualizado com os atributos da Modal
            novaLinha.innerHTML = `
                <td class="py-3 px-3 border-0 text-muted font-monospace small">${codigo}</td>
                <td class="py-3 border-0 fw-medium text-dark">${designacao}</td>
                <td class="py-3 border-0 text-center">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Ativo</span>
                </td>
                <td class="py-3 border-0 text-end pe-3">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-componente px-2" title="Desvincular" data-bs-toggle="modal" data-bs-target="#modalDesvincularComponente">
                        <i class="fa-solid fa-link-slash"></i>
                    </button>
                </td>
            `;

            // Ligar o novo botão à lógica da Modal
            ligarBotaoDesvincular(novaLinha.querySelector('.btn-remover-componente'));

            document.getElementById('corpoTabelaComp').appendChild(novaLinha);
            document.getElementById('contentorTabelaComp').classList.remove('d-none');
            document.getElementById('msgSemComp').classList.add('d-none');
            
            // Fecha o painel
            const painelVincular = document.getElementById('painelVincularComponente');
            if (painelVincular.classList.contains('show')) {
                new bootstrap.Collapse(painelVincular).hide();
            }
            selectComp.value = ""; // Limpa a seleção
        });
    }

    // 4. Ligar os botões das linhas que já vêm no HTML ao carregar a página
    document.querySelectorAll('.btn-remover-componente').forEach(btn => {
        ligarBotaoDesvincular(btn);
    });

    // 5. Executar a remoção apenas quando clica em "Sim" na Modal
    const btnConfirmarDesvincular = document.getElementById('btnConfirmarDesvincular');
    if (btnConfirmarDesvincular) {
        btnConfirmarDesvincular.addEventListener('click', function() {
            if (linhaComponenteAtual) {
                linhaComponenteAtual.remove();
                verificarTabelaComponentes();
                linhaComponenteAtual = null;
                
                // Fechar a modal
                const modalEl = document.getElementById('modalDesvincularComponente');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
            }
        });
    }

    // --------------------------------------------------------
    // 3.2 Alerta (Toast) ao Guardar Alterações
    // --------------------------------------------------------
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

    // --------------------------------------------------------
    // 3.3 Remover Consumíveis (Ação com Modal e Nome Dinâmico)
    // --------------------------------------------------------
    let linhaAtualConsumivel = null;

    // Função que ensina o botão a ir ler o nome do consumível antes de abrir a modal
    function ligarBotaoRemoverConsumivel(botao) {
        botao.addEventListener('click', function() {
            linhaAtualConsumivel = this.closest('tr');
            
            // Vai à primeira coluna da linha e procura o <input> que tem o texto
            const inputNome = linhaAtualConsumivel.querySelector('td:nth-child(1) input');
            const nomeConsumivel = inputNome ? inputNome.value : "Consumível não especificado";
            
            // Escreve o nome capturado na Modal
            const modalTexto = document.getElementById('textoConsumivelModal');
            if (modalTexto) {
                // Se a caixa estiver em branco (value vazio), mete um texto de segurança
                modalTexto.innerText = nomeConsumivel.trim() === "" ? "Consumível sem nome" : nomeConsumivel;
            }
        });
    }

    // Ligar os botões de remover das linhas que já vêm carregadas no HTML
    document.querySelectorAll('.btn-abrir-modal-remover').forEach(botao => {
        ligarBotaoRemoverConsumivel(botao);
    });

    // Ação principal de confirmar a remoção ("Sim" na Modal)
    const btnConfirmarRemocaoConsumivel = document.getElementById('btnConfirmarRemoverConsumivel');
    if (btnConfirmarRemocaoConsumivel) {
        btnConfirmarRemocaoConsumivel.addEventListener('click', function() {
            if (linhaAtualConsumivel) {
                linhaAtualConsumivel.remove();
                
                // Verifica se a tabela ficou vazia para mostrar a mensagem
                const corpoConsumiveis = document.getElementById('corpoTabelaConsumiveis');
                if (corpoConsumiveis && corpoConsumiveis.children.length === 0) {
                    document.getElementById('contentorTabelaConsumiveis').classList.add('d-none');
                    const msgVazia = document.getElementById('msgSemConsumiveis');
                    if (msgVazia) msgVazia.classList.remove('d-none');
                }
                
                linhaAtualConsumivel = null;
                
                // Fechar a modal suavemente
                const modalEl = document.getElementById('modalRemoverConsumivel');
                const modalInstancia = bootstrap.Modal.getInstance(modalEl);
                if (modalInstancia) modalInstancia.hide();
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
const btnGuardarNovoConsumivel = document.getElementById('btnGuardarNovoConsumivel');
    if (btnGuardarNovoConsumivel) {
        btnGuardarNovoConsumivel.addEventListener('click', function() {
            const designacao = document.getElementById('novoConsDesignacao').value;
            const categoria = document.getElementById('novoConsCategoria').value;
            const freq = document.getElementById('novoConsFreq').value;

            if (!designacao || !categoria || !freq) {
                alert("Por favor, preencha a Designação, a Categoria e a Frequência do Consumível.");
                return;
            }

            const novaLinha = document.createElement('tr');
            novaLinha.innerHTML = `
                <td class="py-3 px-3 border-0 fw-medium text-dark">${designacao}</td>
                <td class="py-3 border-0 text-muted">${categoria}</td>
                <td class="py-3 border-0 text-muted">${freq}</td>
                <td class="py-3 pe-3 border-0 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverConsumivel">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            // Dá vida ao botão de lixo desta nova linha
            novaLinha.querySelector('.btn-abrir-modal-remover').addEventListener('click', function() {
                linhaAtualConsumivel = this.closest('tr');
            });

            document.getElementById('corpoTabelaConsumiveis').appendChild(novaLinha);
            document.getElementById('contentorTabelaConsumiveis').classList.remove('d-none');
            document.getElementById('msgSemConsumiveis').classList.add('d-none');

            // Limpa o form e fecha o collapse
            document.getElementById('novoConsDesignacao').value = '';
            document.getElementById('novoConsCategoria').value = '';
            const modalEl = document.getElementById('painelNovoConsumivel');
            if (modalEl.classList.contains('show')) {
                new bootstrap.Collapse(modalEl).hide();
            }
        });
    }