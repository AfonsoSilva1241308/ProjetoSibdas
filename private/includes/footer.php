
 </div>

    <!-- jQuery local -->
    <script src="<?= BASE_URL ?>/assets/jQuery/jquery-3.6.0.min.js"></script>

    <!-- DataTables local -->
    <script src="<?= BASE_URL ?>/assets/datatables/datatables.min.js"></script>

    <!-- Bootstrap JS local -->
    <script src="<?= BASE_URL ?>/assets/Bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Chart.js local -->
    <script src="<?= BASE_URL ?>/assets/chartsjs/chart.umd.min.js"></script>

    <!-- JavaScript próprio -->
    <script src="<?= BASE_URL ?>/assets/js/1241308.js"></script>

<!-- Modal Alterar Password -->
<div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-labelledby="modalAlterarPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAlterarPasswordLabel">
                    <i class="fa-solid fa-key text-primary me-2"></i> Alterar Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?= BASE_URL ?>/private/includes/alterar_password.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_atual" class="form-label fw-semibold">Password Atual</label>
                        <input type="password" class="form-control" id="password_atual" name="password_atual" required>
                    </div>
                    <div class="mb-3">
                        <label for="nova_password" class="form-label fw-semibold">Nova Password</label>
                        <input type="password" class="form-control" id="nova_password" name="nova_password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_password" class="form-label fw-semibold">Confirmar Nova Password</label>
                        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>