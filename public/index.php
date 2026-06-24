<?php
$sucesso_contacto = $_GET['sucesso'] ?? '';
$erro_contacto = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink Digital — Apoio ao Inventário Hospitalar</title>
    
    <link rel="shortcut icon" href="../assets/img/logo1.png" type="image/png">
    <link rel="stylesheet" href="../assets/Bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../assets/css/1241308.css">
</head>
<body>

    <nav class="bng-navbar sticky-top bg-white shadow-sm" style="z-index: 1050;">
        <div>
            <img src="../assets/img/logo.png" alt="Logo da MediLink Digital">
        </div> 

        <div class="container-navegacao">
            <a href="#sobre-nos">Sobre Nós</a>
            <a href="#solucoes">A Nossa Solução</a>
            <a href="#funcionalidades">Funcionalidades da Plataforma</a>
            <a href="#contacto">Contactos</a>
        </div>

        <div class="nav-cliente">
            <a href="login_form.php" target="_blank" class="btn btn-primary fw-bold text-white px-4 rounded-pill">Área Cliente</a>
        </div>
    </nav>

    <div class="banner-hwmed position-relative">
        <img src="../assets/img/hospital.png" class="w-100 object-fit-cover" style="height: 450px;" alt="Tecnologia MediLink">
        
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
        
        <div class="position-absolute top-50 start-50 translate-middle text-center w-100 px-3">
            <h1 class="display-4 fw-bold text-white mb-3">Inteligência na Gestão Hospitalar</h1>
            <p class="lead text-white mb-4 d-none d-md-block">A plataforma inovadora para a monitorização, manutenção e rastreabilidade de equipamentos médicos.</p>
            <a href="#contacto" class="btn btn-primary btn-lg fw-bold px-4">Fale Connosco</a>
        </div>
    </div>

    <div class="container py-5">
        
        <div class="row g-5 align-items-center py-4 mb-5" id="sobre-nos">
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold">Quem Somos</span>
                <h2 class="fw-bold text-dark mb-4" style="font-size: 2.25rem;">Sobre Nós</h2>
                <p class="text-muted fs-5 lh-base">
                    Na <strong>MediLink Digital</strong>, desenvolvemos soluções de software robustas e especializadas em sistemas de informação hospitalar.
                </p>
                <p class="text-muted fs-6 lh-base">
                    O nosso foco é otimizar a gestão de equipamentos clínicos e garantir a evolução, segurança e conformidade do ecossistema tecnológico de saúde em Portugal.
                </p>
            </div>
            
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-primary border-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-hospital text-primary fs-3 me-3"></i>
                                <h3 class="fw-bold text-dark m-0">+45</h3>
                            </div>
                            <span class="text-secondary fw-semibold small d-block">Instituições Alocadas</span>
                            <small class="text-muted">Hospitais e centros clínicos ativos no sistema.</small>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-success border-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-heart-pulse text-success fs-3 me-3"></i>
                                <h3 class="fw-bold text-dark m-0">+1,200</h3>
                            </div>
                            <span class="text-secondary fw-semibold small d-block">Dispositivos Médicos</span>
                            <small class="text-muted">Equipamentos inventariados em tempo real.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="text-muted opacity-25 my-5">

        <div class="row g-5 align-items-center py-4 mb-5" id="solucoes">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-4 text-uppercase small">
                        <i class="fa-solid fa-chart-pie text-primary me-2"></i>Distribuição Global de Equipamentos
                    </h6>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium text-secondary small">Suporte de Vida</span>
                            <span class="fw-bold text-dark small">35%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-danger rounded-pill" role="progressbar" style="width: 35%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium text-secondary small">Monitorização Clínica</span>
                            <span class="fw-bold text-dark small">45%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 45%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium text-secondary small">Diagnóstico / Terapia</span>
                            <span class="fw-bold text-dark small">20%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 order-lg-2 order-1">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold">Gestão Integrada</span>
                <h2 class="fw-bold text-dark mb-4" style="font-size: 2.25rem;">A Nossa Solução</h2>
                <p class="text-muted fs-5 lh-base">
                    A nossa solução digital permite centralizar e organizar a gestão tecnológica dos dispositivos médicos numa única plataforma.
                </p>
                <p class="text-muted fs-6 lh-base">
                    Assegura uma monitorização contínua e uma gestão integrada ao longo de todo o ciclo de vida, desde a entrada em operação até à desativação técnica, garantindo a rastreabilidade e a eficiência operacional.
                </p>
            </div>
        </div>

        <hr class="text-muted opacity-25 my-5">

        <div class="py-4" id="funcionalidades">
            <div class="text-center mb-5">
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold">Módulos do Sistema</span>
                <h2 class="fw-bold text-dark mb-3">Funcionalidades da Plataforma</h2>
                <p class="text-muted fs-5 mx-auto" style="max-width: 800px;">
                    Os equipamentos abrangidos incluem dispositivos médicos com diferentes níveis de criticidade clínica, integrando desde sistemas de monitorização até tecnologias críticas de suporte de vida.
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 py-4">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-server text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title text-dark fw-bold">Inventário Dinâmico</h5>
                            <p class="card-text text-muted mt-3">Registo centralizado de todos os dispositivos médicos com histórico de alocação e estado operacional em tempo real.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 py-4">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-screwdriver-wrench text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title text-dark fw-bold">Manutenção Preventiva</h5>
                            <p class="card-text text-muted mt-3">Agendamento automático e alertas para calibrações e manutenções, garantindo a conformidade clínica e segurança.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 py-4">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-chart-line text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title text-dark fw-bold">Gestão de Ciclo de Vida</h5>
                            <p class="card-text text-muted mt-3">Acompanhamento desde a aquisição até à morte técnica do equipamento, otimizando os custos hospitalares.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white py-5 border-bottom border-top">
        <h2 class="text-center text-primary mb-5 fw-bold">Nossos Clientes</h2>
        <div class="container">
            <div class="row align-items-center justify-content-center text-center g-5">
                
                <div class="col-12 col-md-4 mb-4 mb-md-0"> 
                    <img src="../assets/img/logo-sns.png" alt="SNS - Serviço Nacional de Saúde" style="height: 200px; width: auto; object-fit: contain;">
                </div>
                
                <div class="col-12 col-md-4 mb-4 mb-md-0"> 
                    <img src="../assets/img/logo-saojoao.png" alt="Hospital de São João" style="height: 200px; width: auto; object-fit: contain;">
                </div>
                
                <div class="col-12 col-md-4"> 
                    <img src="../assets/img/logo-santoantonio.png" alt="Hospital de Santo António" style="height: 200px; width: auto; object-fit: contain;">
                </div>

            </div>
        </div>
    </div>
    <section id="contacto" class="py-5 bg-light">
        <div class="container py-5">
            
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold">Apoio e Contacto</span>
                    <h2 class="fw-bold text-primary mb-3">Tem alguma dúvida? Fale connosco.</h2>
                    <p class="text-muted fs-5">
                        A nossa equipa especializada está totalmente disponível para esclarecer qualquer questão relacionada com a plataforma, a sua implementação ou a gestão da engenharia clínica do seu hospital. Envie-nos a sua mensagem!
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">

                        <?php if ($sucesso_contacto === 'mensagem'): ?>
                            <div class="alert alert-success text-center" role="alert">
                                Mensagem enviada com sucesso. Entraremos em contacto brevemente.
                            </div>
                        <?php endif; ?>

                        <?php if ($erro_contacto): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php if ($erro_contacto === 'campos'): ?>
                                    Preenche todos os campos obrigatórios.
                                <?php elseif ($erro_contacto === 'email'): ?>
                                    O email introduzido não é válido.
                                <?php elseif ($erro_contacto === 'tamanho'): ?>
                                    Um dos campos tem texto demasiado longo.
                                <?php else: ?>
                                    Não foi possível enviar a mensagem. Tenta novamente.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form action="processar_contacto.php" method="POST">
                            <div class="row g-4">
                                
                                <div class="col-md-6">
                                    <label for="nome" class="form-label fw-medium text-dark small">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light border-0 py-2" id="nome" name="nome" placeholder="O seu nome" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="instituicao" class="form-label fw-medium text-dark small">Instituição / Hospital <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light border-0 py-2" id="instituicao" name="instituicao" placeholder="Ex: CHUSJ" required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="email" class="form-label fw-medium text-dark small">Email Profissional <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control bg-light border-0 py-2" id="email" name="email" placeholder="nome@hospital.pt" required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="mensagem" class="form-label fw-medium text-dark small">Mensagem</label>
                                    <textarea class="form-control bg-light border-0 py-2" id="mensagem" name="mensagem" rows="4" placeholder="Como podemos ajudar a sua instituição?"></textarea>
                                </div>
                                
                                <div class="col-12 mt-4 pt-2">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold py-3 fs-6 shadow-sm rounded-3">
                                        Enviar Mensagem <i class="fa-solid fa-paper-plane ms-2"></i>
                                    </button>
                                </div>
                                
                                <div class="col-12 text-center mt-2">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Ao enviar, concorda com a nossa <a href="#" class="text-decoration-none">Política de Privacidade</a> e processamento de dados.
                                    </small>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <footer class="footer-container">
        <div class="footer-section">
            <strong>LOCALIZAÇÃO</strong>
            <p>Rua Dr. António Bernardino de Almeida, 431<br>4200-072, Porto<br>Portugal</p>
        </div>
        
        <div class="footer-section">
            <strong>HORÁRIO DE ATENDIMENTO</strong>
            <p>2ª a 6ª Feira: 8h30 — 18h30</p>
            <p>Sábado: 9h00 — 13h00</p>
            <p>Domingo e Feriados: Encerrado</p>
        </div>
        
        <div class="footer-section">
            <strong>CONTACTOS</strong>
            <p>Email: geral@medilinkdigital.pt</p>
            <p>Telefone: +351 220 121 022</p>
        </div>
    </footer>

    <script src="../assets/Bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/1241308.js"></script>
    
</body>
</html>