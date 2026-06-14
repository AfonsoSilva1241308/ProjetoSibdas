<?php include '../includes/header.php'; ?>
    <div class="d-flex vh-100">
         <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="lista.php" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                    </a>
                    <h2 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="fa-solid fa-circle-plus me-2"></i> Registar Novo Fornecedor
                    </h2>
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

            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <form id="formNovoFornecedor" action="gravar_fornecedor.php" method="POST">
    
    <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Entidade</h5>
    
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <label class="form-label text-dark">Nome da Empresa / Marca *</label>
            <input type="text" name="nome_empresa" class="form-control" placeholder="Ex: Dräger Portugal Lda" required>
        </div>
        <div class="col-md-6">
            <label class="form-label text-dark">NIF *</label>
            <input type="text" name="nif" class="form-control" placeholder="Ex: 501234567" maxlength="9" required>
        </div>
        <div class="col-md-6">
            <label class="form-label text-dark">Tipo de Fornecedor *</label>
            <select name="tipo_fornecedor" class="form-select" required>
                <option value="" disabled selected>Selecione uma opção...</option>
                <option value="Fabricante">Fabricante</option>
                <option value="Distribuidor">Distribuidor ou fornecedor comercial</option>
                <option value="Assistencia">Empresa de assistência técnica</option>
                <option value="Consumiveis">Fornecedor de consumíveis ou acessórios</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label text-dark">Contacto Telefónico (Geral) *</label>
            <input type="tel" name="telefone_geral" class="form-control" placeholder="Ex: +351 210 123 456" required>
        </div>
        <div class="col-md-6">
            <label class="form-label text-dark">Email da Empresa *</label>
            <input type="email" name="email_empresa" class="form-control" placeholder="Ex: geral@empresa.pt" required>
        </div>
        <div class="col-md-6">
            <label class="form-label text-dark">Website</label>
            <input type="url" name="website" class="form-control" placeholder="Ex: https://www.empresa.com">
        </div>
        <div class="col-12">
            <label class="form-label text-dark">Morada *</label>
            <input type="text" name="morada" class="form-control" placeholder="Ex: Rua Direita, nº 10, Porto" required>
        </div>
        <div class="col-12">
            <label class="form-label text-dark">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3" placeholder="Notas internas..."></textarea>
        </div>
    </div>

    <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Pessoa de Contacto</h5>
    
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <label class="form-label text-dark">Nome do Contacto *</label>
            <input type="text" name="nome_contacto" class="form-control" placeholder="Ex: Eng. Carlos Mendes" required>
        </div>
        <div class="col-md-4">
            <label class="form-label text-dark">Telefone Pessoal *</label>
            <input type="tel" name="telefone_pessoal" class="form-control" placeholder="Ex: +351 912 345 678" required>
        </div>
        <div class="col-md-4">
            <label class="form-label text-dark">Email Pessoal *</label>
            <input type="email" name="email_pessoal" class="form-control" placeholder="Ex: carlos.mendes@empresa.pt" required>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
        <button type="reset" class="btn btn-outline-secondary px-4 fw-medium">
            <i class="fa-solid fa-eraser me-1"></i> Limpar
        </button>
        <button type="submit" class="btn btn-primary px-5 fw-bold">
            <i class="fa-regular fa-floppy-disk me-1"></i> Guardar Registo
        </button>
    </div>
</form>

                </div>
            </div>

        </div>
    </div>

           <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
              <div id="sucessoToastFornecedor" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                 <div class="d-flex">
                   <div class="toast-body fw-medium fs-6">
                    <i class="fa-solid fa-circle-check me-2"></i> Fornecedor registado com sucesso no sistema!
                 </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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