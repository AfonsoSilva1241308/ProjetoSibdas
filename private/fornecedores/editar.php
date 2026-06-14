<?php include '../includes/header.php'; ?>

    <div class="d-flex vh-100">
     <?php include '../includes/sidebar.php'; ?>


        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="lista.php" class="btn btn-sm btn-outline-secondary px-3">&larr; Voltar</a>
                </div>
                <div class="dropdown m-0">
                    <button class="btn btn-light border shadow-sm fw-medium text-secondary dropdown-toggle" type="button" id="dropdownUtilizador" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-circle me-1"></i> Administrador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="dropdownUtilizador">
                        <li>
                            <a class="dropdown-item text-secondary fw-medium py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalAlterarPassword">
                                <i class="fa-solid fa-key me-2 text-primary"></i> Alterar Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-secondary fw-medium py-2" href="../../public/index.php">
                                <i class="fa-solid fa-right-from-bracket me-2 text-primary"></i> Sair da Conta
                            </a>
                        </li>
                    </ul>
                </div>
            </div> 
                        
            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                    <div class="card-body p-4 p-md-5">
                        
                        <h2 class="mb-4 text-primary"><strong><i class="fa-solid fa-pen-to-square me-2"></i> Atualização de Dados </strong></h2>
                        <p class="text-muted mb-4">A modificar o registo: <strong>Dräger Portugal Lda</strong></p>
                        <hr class="mb-5">
                        
                        <form id="formEditarFornecedor" action="atualizar_fornecedor.php" method="POST">
                            
                            <input type="hidden" name="nif_original" value="501234567">
                            
                            <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Entidade</h5>
                            
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Nome da Empresa / Marca *</label>
                                    <input type="text" name="nome_empresa" class="form-control" value="Dräger Portugal Lda" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">NIF (Não editável) *</label>
                                    <input type="text" name="nif" class="form-control bg-light" value="501234567" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Tipo de Fornecedor *</label>
                                    <select name="tipo_fornecedor" class="form-select" required>
                                        <option value="Fabricante" selected>Fabricante</option>
                                        <option value="Distribuidor">Distribuidor ou fornecedor comercial</option>
                                        <option value="Assistencia">Empresa de assistência técnica</option>
                                        <option value="Consumiveis">Fornecedor de consumíveis ou acessórios</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Contacto Telefónico (Geral) *</label>
                                    <input type="tel" name="telefone_geral" class="form-control" value="+351 210 123 456" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Email da Empresa *</label>
                                    <input type="email" name="email_empresa" class="form-control" value="geral@draeger.pt" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Website</label>
                                    <input type="url" name="website" class="form-control" value="https://www.draeger.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-dark">Morada *</label>
                                    <input type="text" name="morada" class="form-control" value="Rua Professor Doutor Egas Moniz, nº 100, 2745-000 Queluz" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-dark">Observações</label>
                                    <textarea name="observacoes" class="form-control" rows="3">Contrato Full-Risk ativo para os ventiladores da Ala de Cuidados Intensivos.</textarea>
                                </div>
                            </div>

                            <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Pessoa de Contacto</h5>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-dark">Nome do Contacto *</label>
                                    <input type="text" name="nome_contacto" class="form-control" value="Eng. Carlos Mendes" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-dark">Telefone Direto *</label>
                                    <input type="tel" name="telefone_pessoal" class="form-control" value="+351 912 345 678" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-dark">Email Direto *</label>
                                    <input type="email" name="email_pessoal" class="form-control" value="carlos.mendes@draeger.pt" required>
                                </div>
                            </div>

                            <h5 class="text-dark mb-4 border-bottom pb-2 mt-5">Equipamentos Associados</h5>
                            
                            <div class="row g-2 mb-4 bg-light p-3 rounded border">
                                <div class="col-md-8">
                                    <select name="novo_equipamento_id" class="form-select border-secondary">
                                        <option selected disabled>Selecione um equipamento do inventário para associar...</option>
                                        <option value="1">Bomba de Infusão Alaris (EQ-BMB-12)</option>
                                        <option value="2">Monitor de Sinais Vitais (EQ-MON-05)</option>
                                        <option value="3">Desfibrilhador ZOLL (EQ-DEF-02)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="acao_associar" class="btn btn-outline-primary w-100 fw-medium">
                                        <i class="fa-solid fa-link me-2"></i> Associar Equipamento
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive border rounded mb-2">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold text-muted border-0">Código</th>
                                            <th class="small text-uppercase fw-bold text-muted border-0">Equipamento</th>
                                            <th class="small text-uppercase fw-bold text-muted border-0 text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium text-dark">EV500-2021</td>
                                            <td>Ventilador Pulmonar Evita V500</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2" title="Remover Associação" data-bs-toggle="modal" data-bs-target="#modalDesassociar">
                                                    <i class="fa-solid fa-link-slash"></i> Desassociar
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <small class="text-muted d-block mb-4"><i class="fa-solid fa-circle-info me-1"></i> A desassociação não elimina o equipamento do sistema, apenas remove a ligação a este fornecedor.</small>
                            
                            <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                                <a href="lista.php" class="btn btn-light border px-4 d-flex align-items-center">
                                    <i class="fa-solid fa-xmark me-2"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Alterações
                                </button>
                            </div>
                            
                            <div class="alert alert-danger text-center d-none" role="alert" id="mensagemErro">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> Ocorreu um erro ao atualizar o fornecedor. Verifique se preencheu todos os campos corretamente.
                            </div>
                        </form>

                    </div>
                </div>
            </div> </div>
    </div>
    
    <div class="modal fade" id="modalDesassociar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body text-center p-5">
                    
                    <i class="fa-solid fa-link-slash text-warning mb-4" style="font-size: 4rem;"></i>
                    
                    <h5 class="text-dark mb-2">Deseja desassociar este equipamento?</h5>
                    <h4 class="fw-bold text-dark mb-4">Ventilador Pulmonar Evita V500</h4>
                    
                    <div class="mb-4">
                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Código Interno</span>
                        <span class="badge bg-light text-dark border fs-6">EV500-2021</span>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                        </button>
                        
                        <form action="remover_associacao.php" method="POST" class="m-0">
                            <input type="hidden" name="id_equipamento" value="EV500-2021">
                            <input type="hidden" name="nif_fornecedor" value="501234567">
                            <button type="submit" class="btn btn-danger fw-medium px-4 py-2">
                                <i class="fa-solid fa-check me-2"></i> Sim
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 mt-3 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-key text-primary me-2"></i> Alterar Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="alterar_password.php" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password Atual</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_atual" class="form-control bg-light border-end-0" id="passAtual" placeholder="Introduza a password atual" required>
                            <button class="btn bg-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passAtual', 'iconAtual')">
                                <i class="fa-solid fa-eye" id="iconAtual"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_nova" class="form-control border-end-0" id="passNova" placeholder="Mínimo 8 caracteres" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passNova', 'iconNova')">
                                <i class="fa-solid fa-eye" id="iconNova"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Confirmar Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_confirma" class="form-control border-end-0" id="passConfirma" placeholder="Repita a nova password" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passConfirma', 'iconConfirma')">
                                <i class="fa-solid fa-eye" id="iconConfirma"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-4 pt-3">
                        <button type="button" class="btn btn-light border fw-medium px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-medium px-4">Guardar Nova Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>