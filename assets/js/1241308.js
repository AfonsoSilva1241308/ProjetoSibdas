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
    
    if (typeof Chart !== 'undefined') {
        
        // ----------------------------------------------------
        // 1. GRÁFICOS DINÂMICOS (Ligados à Base de Dados)
        // ----------------------------------------------------

        const canvasServicos = document.getElementById('graficoServicos');
        if (canvasServicos && typeof DADOS_SERVICOS !== 'undefined') {
            
            // TRUQUE DE MESTRE: Dividir os nomes compridos em duas linhas (arrays)
            // Assim a letra pode ser maior e manter-se perfeitamente horizontal!
            const labelsServ = DADOS_SERVICOS.map(item => {
                if (item.servico === 'Cuidados Intensivos (UCI)') return ['Cuidados', 'Intensivos (UCI)'];
                if (item.servico === 'Urgência Geral') return ['Urgência', 'Geral'];
                if (item.servico === 'Bloco Operatório') return ['Bloco', 'Operatório'];
                return item.servico;
            });

            const dataServ = DADOS_SERVICOS.map(item => item.total);
            const dataSV   = DADOS_SERVICOS.map(item => item.total_sv);

            new Chart(canvasServicos.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labelsServ,
                    datasets: [
                        { label: 'Nº Equipamentos', data: dataServ, backgroundColor: '#0d6efd', borderRadius: 4 },
                        { label: 'Suporte de Vida', data: dataSV, backgroundColor: '#dc3545', borderRadius: 4 }
                    ]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { position: 'top' } }, 
                    scales: { 
                        x: {
                            ticks: {
                                maxRotation: 0, 
                                minRotation: 0, 
                                autoSkip: false,
                                font: { size: 12 } // A letra volta a ficar grande e legível!
                            }
                        },
                        y: { 
                            beginAtZero: true, 
                            grace: 4, 
                            grid: { borderDash: [4, 4] }, 
                            ticks: { stepSize: 1 } 
                        } 
                    } 
                }
            });
        }

        const canvasCriticidade = document.getElementById('graficoCriticidade');
        if (canvasCriticidade && typeof DADOS_CRITICIDADE !== 'undefined') {
            
            const pesosCrit = { 'Baixa': 1, 'Média': 2, 'Media': 2, 'Alta': 3, 'Suporte de Vida': 4 };
            DADOS_CRITICIDADE.sort((a, b) => (pesosCrit[a.criticidade] || 0) - (pesosCrit[b.criticidade] || 0));

            const labelsCrit = DADOS_CRITICIDADE.map(item => item.criticidade + ': ' + item.total);
            const dataCrit = DADOS_CRITICIDADE.map(item => item.total);
            
            const coresCrit = DADOS_CRITICIDADE.map(item => {
                if (item.criticidade === 'Suporte de Vida') return '#dc3545';
                if (item.criticidade === 'Alta') return '#fd7e14';
                if (item.criticidade === 'Média' || item.criticidade === 'Media') return '#ffc107';
                return '#198754';
            });

            new Chart(canvasCriticidade.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labelsCrit,
                    datasets: [{ data: dataCrit, backgroundColor: coresCrit, borderWidth: 0, hoverOffset: 6 }]
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
      
        // ==========================================
        // GRÁFICO: PRINCIPAIS FORNECEDORES (À PROVA DE BALA)
        // ==========================================
        const canvasFornecedores = document.getElementById('graficoFornecedores');
        if (canvasFornecedores) {
            try {
                // Proteção: Garante que os dados são um array. Se o PHP falhar, cria um array vazio [].
                const dadosForn = (typeof DADOS_FORNECEDORES !== 'undefined' && Array.isArray(DADOS_FORNECEDORES)) ? DADOS_FORNECEDORES : [];
                
                let labelsForn = dadosForn.map(item => item.nome);
                let dataForn = dadosForn.map(item => item.total);

                // Se a base de dados não devolver nenhum fornecedor, desenha uma barra a 0 
                // para o ecrã não ficar num buraco branco!
                if (labelsForn.length === 0) {
                    labelsForn = ['Sem Dados'];
                    dataForn = [0];
                }

                new Chart(canvasFornecedores.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsForn,
                        datasets: [{ label: 'Nº de Equipamentos', data: dataForn, backgroundColor: '#0d6efd', borderRadius: 4 }]
                    },
                    options: { 
                        indexAxis: 'y', 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } }, 
                        scales: { x: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1 } } } 
                    }
                });
            } catch (erro) {
                console.error("Erro no gráfico de Fornecedores:", erro);
            }
        }

        // ==========================================
        // GRÁFICO: IDADE DOS EQUIPAMENTOS (À PROVA DE BALA)
        // ==========================================
        const canvasIdade = document.getElementById('graficoIdade');
        if (canvasIdade) {
            try {
                // Proteção: Se a BD não enviar dados (porque é tudo NULL), assume 0 para tudo
                const d = (typeof DADOS_IDADE !== 'undefined' && DADOS_IDADE) ? DADOS_IDADE : {};
                const dataIdade = [
                    d.age_0_2 || 0,
                    d.age_2_5 || 0,
                    d.age_5_10 || 0,
                    d.age_10_plus || 0
                ];

                new Chart(canvasIdade.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['< 2 Anos', '2 a 5 Anos', '5 a 10 Anos', ['> 10 Anos', '(Fim de Vida)']],
                        datasets: [{ label: 'Número de Equipamentos', data: dataIdade, backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545'], borderRadius: 4 }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } }, 
                        scales: { 
                            x: { ticks: { maxRotation: 0, minRotation: 0 } }, 
                            y: { beginAtZero: true, grace: 1, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1 } } 
                        } 
                    }
                });
            } catch (erro) {
                console.error("Erro no gráfico de Idade:", erro);
            }
        }
} else {
        console.warn("Chart.js não encontrado. Os gráficos não serão carregados.");
    }
}); // <--- ESTE FECHO É OBRIGATÓRIO PARA A FUNÇÃO DO DOMContentLoaded
// ==========================================
// 3. PÁGINA DE EDITAR / DETALHES
// ==========================================
// ==========================================
// 3. PÁGINA DE EDITAR / DETALHES / NOVO
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    const checkEComponente = document.getElementById('checkEComponente');
    const blocoPai = document.getElementById('blocoEquipamentoPai');
    const blocoFilhos = document.getElementById('blocoGerirFilhos'); // Opcional (só existe no Editar)
    
    if (checkEComponente && blocoPai) {
        checkEComponente.addEventListener('change', function() {
            if (this.checked) {
                // É um COMPONENTE: Mostra o dropdown do Pai
                blocoPai.classList.remove('d-none');
                // Se o bloco de filhos existir (página Editar), esconde-o
                if (blocoFilhos) blocoFilhos.classList.add('d-none');
            } else {
                // É EQUIPAMENTO PRINCIPAL: Esconde o dropdown do Pai
                blocoPai.classList.add('d-none');
                // Se o bloco de filhos existir (página Editar), mostra-o
                if (blocoFilhos) blocoFilhos.classList.remove('d-none');
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
    // Se já existirem no teu código noutro sítio, remove as declarações lá!
    if (typeof window.linhaAtualConsumivel === 'undefined') {
        window.linhaAtualConsumivel = null;
    }
    if (typeof window.linhaAtualDoc === 'undefined') {
        window.linhaAtualDoc = null;
    }

    // =========================================================
    // 1. SCRIPT PARA ADICIONAR CONSUMÍVEIS
    // =========================================================
    const btnGuardarNovoConsumivel = document.getElementById('btnGuardarNovoConsumivel');
    if (btnGuardarNovoConsumivel) {
        // Removemos eventuais ouvintes duplicados se necessário
        btnGuardarNovoConsumivel.onclick = null; 
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
                <td class="py-3 px-3 border-0 fw-medium text-dark">
                    ${designacao}
                    <input type="hidden" name="lista_cons_designacao[]" value="${designacao}">
                </td>
                <td class="py-3 border-0 text-muted">
                    ${categoria}
                    <input type="hidden" name="lista_cons_categoria[]" value="${categoria}">
                </td>
                <td class="py-3 border-0 text-muted">
                    <span class="badge bg-light text-dark border">${freq}</span>
                    <input type="hidden" name="lista_cons_frequencia[]" value="${freq}">
                </td>
                <td class="py-3 pe-3 border-0 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverConsumivel">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            novaLinha.querySelector('.btn-abrir-modal-remover').addEventListener('click', function() {
                window.linhaAtualConsumivel = this.closest('tr');
            });

            document.getElementById('corpoTabelaConsumiveis').appendChild(novaLinha);
            document.getElementById('contentorTabelaConsumiveis').classList.remove('d-none');
            document.getElementById('msgSemConsumiveis').classList.add('d-none');

            document.getElementById('novoConsDesignacao').value = '';
            document.getElementById('novoConsCategoria').value = '';
            document.getElementById('novoConsFreq').value = '';
            
            const modalEl = document.getElementById('painelNovoConsumivel');
            if (modalEl && modalEl.classList.contains('show')) {
                let bsCollapse = bootstrap.Collapse.getInstance(modalEl);
                if (!bsCollapse) bsCollapse = new bootstrap.Collapse(modalEl);
                bsCollapse.hide();
            }
        });
    }
// 1. Declarar a variável global de forma que o JS a veja sempre
window.linhaAtualConsumivel = null;

// 2. Delegação de eventos para capturar o clique no "Lixo"
document.addEventListener('click', function(e) {
    const botao = e.target.closest('.btn-abrir-modal-remover');
    if (botao) {
        window.linhaAtualConsumivel = botao.closest('tr');
        
        // Captura os dados da linha (Designação e Categoria)
        const designacaoCons = window.linhaAtualConsumivel.querySelector('td:nth-child(1)').innerText.trim();
        const categoriaCons = window.linhaAtualConsumivel.querySelector('td:nth-child(2)').innerText.trim();
        
        // Preenche o Modal
        const elementoNomeCons = document.getElementById('nomeConsModal');
        const elementoCatCons = document.getElementById('catConsModal');
        
        if (elementoNomeCons) elementoNomeCons.innerText = designacaoCons;
        if (elementoCatCons) elementoCatCons.innerText = categoriaCons;
    }
});

// 3. Botão de confirmação de remoção
const btnConfirmarRemocaoCons = document.getElementById('btnConfirmarRemocaoConsumivel');
if (btnConfirmarRemocaoCons) {
    btnConfirmarRemocaoCons.addEventListener('click', function() {
        if (window.linhaAtualConsumivel) {
            window.linhaAtualConsumivel.remove(); // Remove a linha
            window.linhaAtualConsumivel = null;   // Limpa a variável
            
            // Fecha o modal
            const modalElement = document.getElementById('modalRemoverConsumivel');
            const modalInstancia = bootstrap.Modal.getInstance(modalElement);
            if (modalInstancia) modalInstancia.hide();
            
            // Verifica se a tabela ficou vazia
            const corpoCons = document.getElementById('corpoTabelaConsumiveis');
            if (corpoCons && corpoCons.children.length === 0) {
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
                <td class="py-3 px-3 border-0 fw-medium text-dark">
                    ${designacao}
                    <input type="hidden" name="lista_cons_designacao[]" value="${designacao}">
                </td>
                <td class="py-3 border-0 text-muted">
                    ${categoria}
                    <input type="hidden" name="lista_cons_categoria[]" value="${categoria}">
                </td>
                <td class="py-3 border-0 text-muted">
                    <span class="badge bg-light text-dark border">${freq}</span>
                    <input type="hidden" name="lista_cons_frequencia[]" value="${freq}">
                </td>
                <td class="py-3 pe-3 border-0 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverConsumivel">
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

            // CORREÇÃO DO ERRO: Limpar manualmente as caixas em vez de fazer .reset() numa DIV
            document.getElementById('novoDocTitulo').value = '';
            document.getElementById('novoDocCategoria').value = '';
            document.getElementById('novoDocFicheiro').value = '';
            document.getElementById('novoDocFornecedor').value = '';
            document.getElementById('novoDocValidade').value = '';
            document.getElementById('alertaExp').checked = false;

            // Fecha o painel de adicionar documento
            const modalDoc = document.getElementById('painelNovoDocumento');
            if (modalDoc.classList.contains('show')) {
                let bsCollapseDoc = bootstrap.Collapse.getInstance(modalDoc);
                if (!bsCollapseDoc) bsCollapseDoc = new bootstrap.Collapse(modalDoc);
                bsCollapseDoc.hide();
            }
        });

}
});

    function atualizarHoraDashboard() {
    const elementoHora = document.getElementById('tempo-atualizacao');
    
    if (elementoHora) {
        const agora = new Date();
        // Obtém as horas e os minutos garantindo que têm sempre 2 dígitos (ex: 09 em vez de 9)
        const horas = String(agora.getHours()).padStart(2, '0');
        const minutos = String(agora.getMinutes()).padStart(2, '0');
        
        // Injeta o HTML mantendo o teu ícone original do FontAwesome
        elementoHora.innerHTML = `<i class="fa-regular fa-clock me-1"></i> Atualizado hoje às ${horas}:${minutos}`;
    }
}

// Garante que o script corre assim que o HTML terminar de carregar no navegador
document.addEventListener('DOMContentLoaded', atualizarHoraDashboard);

   $(document).ready(function() {

    // ==========================================
    // 1. DATATABLES E PESQUISA: EQUIPAMENTOS
    // ==========================================
    if ($('#tabela-equipamentos').length) {
        
        var tabela = $('#tabela-equipamentos').DataTable({
            pageLength: 7,
            dom: 't', 
            language: {
                emptyTable: "Sem dados disponíveis na tabela.",
                zeroRecords: "Nenhum equipamento encontrado."
            }
        });

        // Barra de pesquisa HTML
        $('input[name="pesquisa"]').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        $('input[name="pesquisa"]').closest('form').on('submit', function(e) {
            e.preventDefault();
        });

        // Paginação Customizada Equipamentos
        tabela.on('draw', function () {
            var info = tabela.page.info();
            $('#total-registos-custom').text(info.recordsDisplay);

            var paginacaoHTML = '';
            var btnAnteriorClass = (info.page === 0) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnAnteriorClass + '"><a class="page-link" href="#" data-page="previous">Anterior</a></li>';
            
            for (var i = 0; i < info.pages; i++) {
                var btnNumeroClass = (info.page === i) ? 'active' : '';
                paginacaoHTML += '<li class="page-item ' + btnNumeroClass + '"><a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
            }
            
            var btnProximaClass = (info.page === info.pages - 1) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnProximaClass + '"><a class="page-link" href="#" data-page="next">Próxima</a></li>';
            
            $('#paginacao-custom').html(paginacaoHTML);
        });

        tabela.draw();

        $('#paginacao-custom').on('click', '.page-link', function(e) {
            e.preventDefault();
            var acao = $(this).attr('data-page');
            
            if (acao === 'previous' || acao === 'next') {
                tabela.page(acao).draw('page');
            } else if (acao !== undefined) {
                tabela.page(parseInt(acao)).draw('page');
            }
        });

        // ==========================================
        // ORDENAÇÃO: DROPDOWN (A-Z, Recentes, etc.)
        // ==========================================
        $('select[name="ordenacao"]').on('change', function() {
            var tipoOrdenacao = $(this).val();

            if (tipoOrdenacao === 'az') {
                tabela.order([1, 'asc']).draw();  // Coluna 1: Designação (A-Z)
            } 
            else if (tipoOrdenacao === 'za') {
                tabela.order([1, 'desc']).draw(); // Coluna 1: Designação (Z-A)
            } 
            else if (tipoOrdenacao === 'recentes') {
                tabela.order([0, 'desc']).draw(); // Coluna 0: Código (Mais novos primeiro)
            } 
            else if (tipoOrdenacao === 'criticidade') {
                // Agora sim! Ordena pelo data-sort numérico de forma descendente (4 -> 1)
                // Resultado: Suporte de Vida > Alta > Média > Baixa
                tabela.order([3, 'desc']).draw(); 
            }
        });
        // FILTROS AVANÇADOS: EQUIPAMENTOS
        // ==========================================
        $('#filtrosAvancados').closest('form').on('submit', function(e) {
            e.preventDefault(); // Impede o reload da página
            
            // Vai ler o texto visível que o utilizador escolheu na dropdown
            var cat  = $('select[name="categoria"]').val() ? $('select[name="categoria"] option:selected').text() : '';
            var est  = $('select[name="estado"]').val() ? $('select[name="estado"] option:selected').text() : '';
            var crit = $('select[name="criticidade"]').val() ? $('select[name="criticidade"] option:selected').text() : '';

            // Aplica a pesquisa nas colunas exatas da tua tabela
            tabela.column(1).search(cat)   // Procura Categoria na Coluna 1 (Designação)
                  .column(4).search(est)   // Procura Estado na Coluna 4 (Estado Atual)
                  .column(3).search(crit)  // Procura Criticidade na Coluna 3 (Criticidade)
                  .draw();
        });

        // Botão Limpar Tudo (Reset)
        $('button[type="reset"]').on('click', function() {
            setTimeout(function() { 
                tabela.columns().search('').draw();
            }, 10);
        });
    }

    // ==========================================
    // 2. DATATABLES: LOCALIZAÇÕES
    // ==========================================
    if ($('#tabela-localizacoes').length) {
        
        var tabelaLoc = $('#tabela-localizacoes').DataTable({
            pageLength: 7,
            dom: 't', 
            language: {
                emptyTable: "Sem dados disponíveis na tabela.",
                zeroRecords: "Nenhuma localização encontrada."
            }
        });

        // Ligar a barra de pesquisa
        $('input[name="pesquisa"]').on('keyup', function() {
            tabelaLoc.search(this.value).draw();
        });
        
        $('input[name="pesquisa"]').closest('form').on('submit', function(e) {
            e.preventDefault();
        });

        // Paginação Customizada Localizações
        tabelaLoc.on('draw', function () {
            var info = tabelaLoc.page.info();
            
            $('#total-registos-loc').text(info.recordsDisplay);

            var paginacaoHTML = '';
            var btnAnteriorClass = (info.page === 0) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnAnteriorClass + '"><a class="page-link" href="#" data-page="previous">Anterior</a></li>';
            
            for (var i = 0; i < info.pages; i++) {
                var btnNumeroClass = (info.page === i) ? 'active' : '';
                paginacaoHTML += '<li class="page-item ' + btnNumeroClass + '"><a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
            }
            
            var btnProximaClass = (info.page === info.pages - 1) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnProximaClass + '"><a class="page-link" href="#" data-page="next">Próxima</a></li>';
            
            $('#paginacao-loc').html(paginacaoHTML);
        });

        tabelaLoc.draw();

        $('#paginacao-loc').on('click', '.page-link', function(e) {
            e.preventDefault();
            var acao = $(this).attr('data-page');
            if (acao === 'previous' || acao === 'next') {
                tabelaLoc.page(acao).draw('page');
            } else if (acao !== undefined) {
                tabelaLoc.page(parseInt(acao)).draw('page');
            }
        });
    }

    // ==========================================
    // 3. DATATABLES: FORNECEDORES
    // ==========================================
    if ($('#tabela-fornecedores').length) {
        
        var tabelaForn = $('#tabela-fornecedores').DataTable({
            pageLength: 7, // Mostra 8 fornecedores por página
            dom: 't', 
            language: {
                emptyTable: "Sem dados disponíveis.",
                zeroRecords: "Nenhum fornecedor encontrado na pesquisa."
            }
        });

        // Ligar barra de pesquisa
        $('input[name="pesquisa"]').on('keyup', function() {
            tabelaForn.search(this.value).draw();
        });

        // Evitar que o form dê reload à página ao carregar no "Enter"
        $('input[name="pesquisa"]').closest('form').on('submit', function(e) {
            e.preventDefault();
        });

        // Paginação e Contagem
        tabelaForn.on('draw', function () {
            var info = tabelaForn.page.info();
            $('#total-registos-forn').text(info.recordsDisplay);

            var paginacaoHTML = '';
            var btnAnteriorClass = (info.page === 0) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnAnteriorClass + '"><a class="page-link" href="#" data-page="previous">Anterior</a></li>';
            
            for (var i = 0; i < info.pages; i++) {
                var btnNumeroClass = (info.page === i) ? 'active' : '';
                paginacaoHTML += '<li class="page-item ' + btnNumeroClass + '"><a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
            }
            
            var btnProximaClass = (info.page === info.pages - 1) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnProximaClass + '"><a class="page-link" href="#" data-page="next">Próxima</a></li>';
            
            $('#paginacao-forn').html(paginacaoHTML);
        });

        tabelaForn.draw();

        $('#paginacao-forn').on('click', '.page-link', function(e) {
            e.preventDefault();
            var acao = $(this).attr('data-page');
            if (acao === 'previous' || acao === 'next') {
                tabelaForn.page(acao).draw('page');
            } else if (acao !== undefined) {
                tabelaForn.page(parseInt(acao)).draw('page');
            }
        });
    }
// ==========================================
    // 4. DATATABLES: MANUTENÇÕES
    // ==========================================
    if ($('#tabela-manutencoes').length) {
        
        var tabelaMan = $('#tabela-manutencoes').DataTable({
            pageLength: 6, // Mostra 6 registos para o ecrã não ficar demasiado cheio
            dom: 't', 
            language: {
                emptyTable: "Sem dados disponíveis.",
                zeroRecords: "Nenhuma intervenção encontrada na pesquisa."
            }
        });

        // Ligar barra de pesquisa
        $('input[name="pesquisa_manutencao"]').on('keyup', function() {
            tabelaMan.search(this.value).draw();
        });

        // Impedir reload da página ao dar Enter na barra de pesquisa
        $('input[name="pesquisa_manutencao"]').closest('form').on('submit', function(e) {
            e.preventDefault();
        });

        // Paginação Dinâmica
        tabelaMan.on('draw', function () {
            var info = tabelaMan.page.info();
            $('#total-registos-man').text(info.recordsDisplay);

            var paginacaoHTML = '';
            var btnAnteriorClass = (info.page === 0) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnAnteriorClass + '"><a class="page-link" href="#" data-page="previous">Anterior</a></li>';
            
            for (var i = 0; i < info.pages; i++) {
                var btnNumeroClass = (info.page === i) ? 'active' : '';
                paginacaoHTML += '<li class="page-item ' + btnNumeroClass + '"><a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
            }
            
            var btnProximaClass = (info.page === info.pages - 1) ? 'disabled' : '';
            paginacaoHTML += '<li class="page-item ' + btnProximaClass + '"><a class="page-link" href="#" data-page="next">Próxima</a></li>';
            
            $('#paginacao-man').html(paginacaoHTML);
        });

        // Força a atualização mal a página abre
        tabelaMan.draw();

        // Ação de clicar nos botões da paginação
        $('#paginacao-man').on('click', '.page-link', function(e) {
            e.preventDefault();
            var acao = $(this).attr('data-page');
            if (acao === 'previous' || acao === 'next') {
                tabelaMan.page(acao).draw('page');
            } else if (acao !== undefined) {
                tabelaMan.page(parseInt(acao)).draw('page');
            }
        });
    }
});
