<?php
   require_once(dirname(__FILE__) . '/manager/session_manager.php');
   isSessionValid(true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Banco de Perguntas</title>
    <link rel="stylesheet" href="css/style.min.css">
    <link rel="stylesheet" href="css/loophish-theme.css">
    <link rel="stylesheet" href="css/summernote-lite.min.css">
</head>
<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    
    <div id="main-wrapper">
        <?php include_once 'z_menu.php' ?>
        
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Gerenciar Banco de Perguntas</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Treinamentos</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Banco de Perguntas</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="container-fluid">
    <style>
    /* Fix z-index and positioning for Summernote dialogs inside the admin theme */
    .note-editor .note-editable {background: #fdfdfd; color: rgb(26, 24, 24); min-height: 180px; overflow: auto; }
    .note-editor .note-toolbar { z-index: 1051; }

    /* Ensure the Summernote modal is centered and above everything */
    .note-modal {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        z-index: 30000 !important;
        width: 100%;
        max-width: 900px;
        pointer-events: auto;
    }
    .note-modal .modal-dialog { max-width: 900px; width: 90%; }
    .note-modal .modal-content { background: #fff; color: #000; }

    /* Backdrop should also be above other elements */
    .note-modal-backdrop, .modal-backdrop { z-index: 29999 !important; position: fixed !important; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); }

    /* Prevent editor parent containers from clipping the modal */
    .note-editor, .modern-card, .card-body, .page-wrapper { overflow: visible !important; }

    .note-editor .note-editable p { color: #fff; }
    </style>
                <div class="row">
                    <div class="col-md-6">
                        <div class="modern-card">
                            <div class="card-body">
                                <h5>Adicionar Pergunta</h5>
                                <form id="questionForm">
                                    <div class="form-group">
                                        <label>Pergunta</label>
                                        <input type="text" id="question_text" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select id="question_type" class="form-control">
                                            <option value="multiple_choice">Múltipla Escolha</option>
                                            <option value="true_false">Verdadeiro / Falso</option>
                                            <option value="text">Texto</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Categoria</label>
                                        <select id="category" class="form-control">
                                            <option value="Phishing e Engenharia Social">Phishing e Engenharia Social</option>
                                            <option value="Proteção de Dados e LGPD">Proteção de Dados e LGPD</option>
                                            <option value="Cibersegurança no Dia a Dia">Cibersegurança no Dia a Dia</option>
                                            <option value="Uso Seguro de Ferramentas Corporativas">Uso Seguro de Ferramentas Corporativas</option>
                                            <option value="Fraudes e Golpes Digitais">Fraudes e Golpes Digitais</option>
                                            <option value="Compliance e Cultura de Segurança">Compliance e Cultura de Segurança</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Dificuldade</label>
                                        <select id="difficulty" class="form-control">
                                            <option value="basic">Iniciante</option>
                                            <option value="intermediate">Intermediário</option>
                                            <option value="advanced">Avançado</option>
                                        </select>
                                    </div>
                                    <div id="choices_container" class="form-group">
                                        <label>Opções (marque a correta)</label>
                                        <div id="choices_list">
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="0" checked></div></div>
                                                <input type="text" class="form-control choice_text" placeholder="Opção A">
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="1"></div></div>
                                                <input type="text" class="form-control choice_text" placeholder="Opção B">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_choice">Adicionar Opção</button>
                                    </div>
                                    <div class="form-group">
                                        <label>Explicação</label>
                                        <textarea id="explanation" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Conteúdo de Apoio (HTML ou imagem)</label>
                                        <textarea id="supporting_html" class="form-control" rows="3" placeholder="Cole HTML aqui ou use upload de imagem"></textarea>
                                        <div class="mt-2">
                                            <input type="file" id="supporting_file" accept="image/*" style="display:inline-block">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="upload_supporting">Enviar Imagem</button>
                                            <small class="form-text text-muted">Ao enviar uma imagem, a tag &lt;img&gt; será inserida no campo acima com o caminho do arquivo.</small>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-modern btn-modern-primary">Salvar Pergunta</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modern-card">
                            <div class="card-body">
                                <h5>Lista de Perguntas</h5>
                                <div class="form-inline mb-3">
                                    <select id="filter_category" class="form-control mr-2">
                                        <option value="">Todas as categorias</option>
                                        <option value="Phishing e Engenharia Social">Phishing e Engenharia Social</option>
                                        <option value="Proteção de Dados e LGPD">Proteção de Dados e LGPD</option>
                                        <option value="Cibersegurança no Dia a Dia">Cibersegurança no Dia a Dia</option>
                                        <option value="Uso Seguro de Ferramentas Corporativas">Uso Seguro de Ferramentas Corporativas</option>
                                        <option value="Fraudes e Golpes Digitais">Fraudes e Golpes Digitais</option>
                                        <option value="Compliance e Cultura de Segurança">Compliance e Cultura de Segurança</option>
                                    </select>
                                    <select id="filter_difficulty" class="form-control mr-2">
                                        <option value="">Todas as dificuldades</option>
                                        <option value="basic">Iniciante</option>
                                        <option value="intermediate">Intermediário</option>
                                        <option value="advanced">Avançado</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-secondary" id="btn_filter">Filtrar</button>
                                </div>
                                <div id="questions_list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="js/libs/jquery/jquery-ui.min.js"></script>
    <script src="js/libs/js.cookie.min.js"></script>
    <script src="js/libs/popper.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
    <script src="js/libs/custom.min.js"></script>
    <script src="js/libs/summernote-bs4.min.js"></script>
    <script src="js/common_scripts.js"></script>
    <script src="js/training_questions.js"></script>
    <script defer src="js/libs/sidebarmenu.js"></script>
</body>
</html>
