$(document).ready(function(){
    loadQuestions();

    // Initialize summernote editor for supporting_html
    if ($.fn.summernote && $('#supporting_html').length) {
        $('#supporting_html').summernote({
            height: 300,
            dialogsInBody: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        // Ensure modals are centered if theme overrides exist
        $(document).on('shown.bs.modal', '.note-modal', function(){
            var $m = $(this);
            $m.css({ 'display':'block' });
        });
    }

    $('#question_type').change(function(){
        var t = $(this).val();
        if(t === 'multiple_choice') $('#choices_container').show();
        else $('#choices_container').hide();
    }).trigger('change');

    $('#add_choice').click(function(){
        var container = $('#choices_list');
        var idx = container.find('.input-group').length;
        var letter = String.fromCharCode(65 + idx);
        var html = `\
            <div class="input-group mb-2">\
                <div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="${idx}"></div></div>\
                <input type="text" class="form-control choice_text" placeholder="Opção ${letter}">\
                <div class="input-group-append">\
                    <button type="button" class="btn btn-outline-danger btn-sm remove_choice">\
                        <i class="mdi mdi-delete"></i>\
                    </button>\
                </div>\
            </div>`;
        container.append(html);
    });

    $(document).on('click', '.remove_choice', function(){
        $(this).closest('.input-group').remove();
    });

    $('#questionForm').submit(function(e){
        e.preventDefault();
        var editingId = $('#questionForm').data('editing-id') || '';
        var question_text = $('#question_text').val().trim();
        var question_type = $('#question_type').val();
        var category = $('#category').val();
        var difficulty = $('#difficulty').val();
        var explanation = $('#explanation').val().trim();
        var supporting_html = '';
        if ($.fn.summernote && $('#supporting_html').length) supporting_html = $('#supporting_html').summernote('code'); else supporting_html = $('#supporting_html').val().trim();

        var choices = [];
        var correct = null;
        if(question_type === 'multiple_choice'){
            $('#choices_list .choice_text').each(function(i){
                var v = $(this).val().trim();
                if(v) choices.push(v);
            });
            correct = $('input[name="choice_correct"]:checked').val();
        } else if (question_type === 'true_false'){
            choices = ['Verdadeiro','Falso'];
            correct = '0'; // default true (string)
        } else {
            choices = [];
            correct = '';
        }

        var postData = {
            question_text: question_text,
            question_type: question_type,
            category: category,
            difficulty: difficulty,
            choices: JSON.stringify(choices),
            correct_answer: (correct===null? '': (''+correct)),
            supporting_html: supporting_html,
            explanation: explanation
        };

        if (editingId) {
            postData.action_type = 'update_question';
            postData.question_id = editingId;
        } else {
            postData.action_type = 'add_question';
        }

        $.post('manager/training_manager.php', postData, function(resp){
            if(resp.result === 'success'){
                alert(editingId? 'Pergunta atualizada':'Pergunta adicionada');
                $('#questionForm')[0].reset();
                $('#questionForm').removeData('editing-id');
                $('#choices_list').html(` <div class="input-group mb-2">\n                        <div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="0" checked></div></div>\n                        <input type="text" class="form-control choice_text" placeholder="Opção A">\n                    </div>\n                    <div class="input-group mb-2">\n                        <div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="1"></div></div>\n                        <input type="text" class="form-control choice_text" placeholder="Opção B">\n                    </div>`);
                if ($.fn.summernote && $('#supporting_html').length) $('#supporting_html').summernote('code',''); else $('#supporting_html').val('');
                $('#supporting_file').val('');
                loadQuestions();
            } else {
                alert('Erro: ' + (resp.error||'erro desconhecido'));
            }
        }, 'json').fail(function(){ alert('Erro de comunicação'); });
    });

    $('#btn_filter').click(function(e){ e.preventDefault(); loadQuestions(); });

    // Upload supporting file (image)
    $('#upload_supporting').click(function(){
        var f = $('#supporting_file')[0].files[0];
        if(!f){ alert('Selecione um arquivo primeiro'); return; }
        // Client-side validation: limit file types and size
        var allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        var maxSize = 2 * 1024 * 1024; // 2 MB
        if (allowed.indexOf(f.type) === -1) { alert('Tipo de arquivo não suportado. Use JPG, PNG, GIF ou WEBP.'); return; }
        if (f.size > maxSize) { alert('Arquivo muito grande. Máximo permitido: 2 MB.'); return; }
        var fd = new FormData();
        fd.append('file', f);
        // Tell the manager which action to run so PHP switch picks the upload handler
        fd.append('action_type', 'upload_question_asset');
        $.ajax({
            url: 'manager/training_manager.php',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: fd,
            success: function(resp){
                if(resp.result === 'success'){
                    var imgTag = '<img src="'+resp.path+'" alt="supporting image" style="max-width:100%;height:auto">';
                    if ($.fn.summernote && $('#supporting_html').length) {
                        var cur = $('#supporting_html').summernote('code') || '';
                        $('#supporting_html').summernote('code', cur + '<br/>' + imgTag);
                    } else {
                        var cur = $('#supporting_html').val() || '';
                        $('#supporting_html').val(cur + '\n' + imgTag);
                    }
                    alert('Upload concluído e imagem inserida no campo');
                } else {
                    alert('Upload falhou: '+(resp.error||'erro'));
                }
            },
            error: function(){ alert('Erro de comunicação no upload'); }
        });
    });
});

function loadQuestions(){
    var category = $('#filter_category').val() || '';
    var difficulty = $('#filter_difficulty').val() || '';
    $.post('manager/training_manager.php', { action_type: 'get_questions', category: category, difficulty: difficulty }, function(resp){
        if(resp.result === 'success'){
            var html = '';
            resp.data.forEach(function(q){
                var choices = q.choices ? JSON.parse(q.choices) : [];
                html += `<div class="question-card mb-2 p-2 border" data-question-id="${q.question_id}">\n<h6>${escapeHtml(q.question_text)}</h6>\n<p><small>Categoria: ${q.category||'N/A'} • Dificuldade: ${q.difficulty}</small></p>\n`;
                if(q.supporting_html){
                    html += `<div class="supporting-html">${q.supporting_html}</div>`;
                }
                if(choices.length){
                    html += '<ul>';
                    choices.forEach(function(c,i){
                        var marker = (q.correct_answer !== null && q.correct_answer !== '') && (strEquals(q.correct_answer, i+'') ) ? ' <strong>(Correta)</strong>' : '';
                        html += `<li>${escapeHtml(c)}${marker}</li>`;
                    });
                    html += '</ul>';
                }
                html += `<div><button class="btn btn-sm btn-danger" onclick="deleteQuestion('${q.question_id}')">Excluir</button></div></div>`;
            });
            $('#questions_list').html(html);
            // attach click handler to load question for editing
            $('.question-card').click(function(){
                var id = $(this).data('question-id');
                if(!id) return;
                $.post('manager/training_manager.php', { action_type:'get_question', question_id: id }, function(resp){
                    if(resp.result === 'success'){
                        var q = resp.data;
                        $('#question_text').val(q.question_text);
                        $('#question_type').val(q.question_type).trigger('change');
                        $('#category').val(q.category);
                        $('#difficulty').val(q.difficulty);
                        $('#explanation').val(q.explanation || '');
                        if ($.fn.summernote && $('#supporting_html').length) $('#supporting_html').summernote('code', q.supporting_html || ''); else $('#supporting_html').val(q.supporting_html || '');
                        // Load choices
                        try{ var choices = q.choices ? JSON.parse(q.choices) : []; } catch(e){ var choices = []; }
                        var htmlChoices = '';
                        for(var i=0;i<choices.length;i++){
                            var checked = (''+q.correct_answer === ''+i) ? 'checked' : '';
                            var letter = String.fromCharCode(65 + i);
                            htmlChoices += `<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="choice_correct" value="${i}" ${checked}></div></div><input type="text" class="form-control choice_text" value="${escapeHtml(choices[i])}"></div>`;
                        }
                        $('#choices_list').html(htmlChoices);
                        $('#questionForm').data('editing-id', id);
                    } else {
                        alert('Não foi possível carregar a pergunta');
                    }
                }, 'json');
            });
        } else {
            $('#questions_list').html('<p>Nenhuma pergunta</p>');
        }
    }, 'json');
}

function deleteQuestion(id){
    if(!confirm('Excluir esta pergunta?')) return;
    $.post('manager/training_manager.php', { action_type:'delete_question', question_id: id }, function(resp){
        if(resp.result === 'success') loadQuestions(); else alert('Erro: '+(resp.error||'erro'));
    }, 'json');
}

function escapeHtml(text){ return $('<div/>').text(text).html(); }
function strEquals(a,b){ return (''+a) === (''+b); }
