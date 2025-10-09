# 🎯 GUIA PASSO A PASSO - TESTE COMPLETO DO SISTEMA DE TREINAMENTO

## 📋 OBJETIVO
Testar todo o fluxo do sistema de treinamento: **Criação de Módulo** → **Campanha de Email** → **Usuário faz Treinamento** → **Emissão de Certificado**

---

## 🔧 **PASSO 1: VERIFICAÇÃO DO SISTEMA**

### 1.1 Verificar Status das Tabelas
1. **Abrir no navegador:** `https://loophish.local/spear/test_training_system.php`
2. **Verificar se todas as tabelas estão criadas:**
   - ✅ `tb_training_modules` 
   - ✅ `tb_training_certificates`
   - ✅ `tb_training_progress`
   - ✅ `tb_training_assignments`
   - ✅ `tb_campaign_training_association`

### 1.2 Verificar Interface de Gerenciamento
1. **Acessar:** `https://loophish.local/spear/TrainingManagement.php`
2. **Confirmar que a interface carrega sem erros**
3. **Verificar se os cards de estatísticas aparecem**

---

## 📚 **PASSO 2: CRIAR MÓDULO DE TREINAMENTO**

### 2.1 Acessar Interface de Criação
1. **Ir para:** `TrainingManagement.php`
2. **Clicar em:** "Novo Módulo" ou "Add Module"
3. **Preencher o formulário:**

### 2.2 Dados do Módulo de Teste
```
Nome do Módulo: "Segurança Digital - Teste Phishing"
Descrição: "Módulo de teste para verificar identificação de emails maliciosos"
Tipo: "mixed" (Misto - Vídeo + Quiz)
Categoria: "Segurança Digital"
Nível de Dificuldade: "Básico"
Duração Estimada: "15 minutos"
Pontuação Mínima: "80%"
```

### 2.3 Conteúdo do Módulo
**Conteúdo (HTML/Vídeo):**
```html
<h2>🛡️ Segurança Digital - Identificando Phishing</h2>

<div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3>📺 Vídeo Educativo</h3>
    <div style="text-align: center; background: #007bff; color: white; padding: 40px; border-radius: 10px;">
        <i class="fas fa-play-circle" style="font-size: 48px;"></i>
        <h4>Simulação de Vídeo - Como Identificar Emails Maliciosos</h4>
        <p>Este seria um vídeo explicando técnicas de phishing e como identificá-las.</p>
        <small>⏱️ Duração: 5 minutos</small>
    </div>
</div>

<div style="background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3>📝 Pontos Principais:</h3>
    <ul>
        <li><strong>Verificar remetente:</strong> Sempre conferir o endereço de email</li>
        <li><strong>Links suspeitos:</strong> Não clicar em links duvidosos</li>
        <li><strong>Urgência falsa:</strong> Desconfiar de mensagens "urgentes"</li>
        <li><strong>Dados pessoais:</strong> Nunca fornecer senhas por email</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <button onclick="alert('Vídeo concluído! Agora você pode fazer o quiz.')" 
            style="background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px;">
        ✅ Marcar Vídeo como Assistido
    </button>
</div>
```

### 2.4 Quiz de Teste
**Criar 3 perguntas no quiz:**

**Pergunta 1:**
```
Texto: "Qual é o principal indicador de um email de phishing?"
Tipo: Múltipla escolha
Opções:
- O email tem muitas imagens (incorreta)
- O remetente solicita informações pessoais urgentemente (correta) 
- O email é longo demais (incorreta)
- O email foi enviado à noite (incorreta)
```

**Pergunta 2:**
```
Texto: "O que você deve fazer ao receber um email suspeito?"
Tipo: Múltipla escolha
Opções:
- Reencaminhar para todos os contatos (incorreta)
- Clicar no link para verificar (incorreta)
- Deletar e reportar como spam (correta)
- Responder perguntando se é real (incorreta)
```

**Pergunta 3:**
```
Texto: "Verdadeiro ou Falso: Bancos enviam emails solicitando senhas"
Tipo: Verdadeiro/Falso
Resposta Correta: Falso
```

### 2.5 Salvar o Módulo
1. **Clicar em:** "Salvar Módulo" ou "Save Module"
2. **Anotar o ID do módulo** que foi criado
3. **Verificar se aparece na lista de módulos**

---

## 📧 **PASSO 3: CRIAR CAMPANHA DE EMAIL COM INTEGRAÇÃO**

### 3.1 Criar Grupo de Usuários
1. **Ir para:** `MailUserGroup.php`
2. **Criar novo grupo:** "Teste Treinamento"
3. **Adicionar um usuário de teste:**
   ```
   Nome: João
   Sobrenome: Silva
   Email: joao.teste@exemplo.com
   Notes: TI
   ```

### 3.2 Criar Template de Email
1. **Ir para:** `MailTemplate.php`
2. **Criar template:** "Email Teste Treinamento"
3. **Conteúdo do email:**
```html
<h2>🏦 Banco Seguro - Atualização Necessária</h2>

<p>Prezado {{NAME}},</p>

<p>Detectamos atividade suspeita em sua conta. Para sua segurança, 
é necessário atualizar suas informações imediatamente.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="https://loophishx.com/track?rid={{RID}}" 
       style="background: #dc3545; color: white; padding: 15px 30px; 
              text-decoration: none; border-radius: 5px; font-weight: bold;">
        🔒 ATUALIZAR DADOS AGORA
    </a>
</div>

<p><small>Se você não atualizar em 24h, sua conta será suspensa.</small></p>

<p>Atenciosamente,<br>
Equipe de Segurança do Banco</p>
```

### 3.3 Criar Campanha de Email
1. **Ir para:** `MailCampaignList.php`
2. **Criar nova campanha:** "Teste Treinamento Phishing"
3. **Configurar:**
   - **Grupo de usuários:** "Teste Treinamento"
   - **Template:** "Email Teste Treinamento"  
   - **Remetente:** Configurar um remetente teste
   
### 3.4 Configurar Integração com Treinamento
1. **Na configuração da campanha**
2. **Procurar seção "Training Integration"**
3. **Ativar:** "Enable Training Integration"
4. **Selecionar:** O módulo "Segurança Digital - Teste Phishing"
5. **Tipo:** "Post Submit" (após clique no link)

---

## 🎯 **PASSO 4: EXECUTAR CAMPANHA E SIMULAR USUÁRIO**

### 4.1 Enviar Campanha
1. **Voltar para lista de campanhas**
2. **Clicar em:** "Enviar" ou "Send" na campanha criada
3. **Confirmar envio**

### 4.2 Verificar Email (Simulação)
1. **Ir para:** "Web Tracker" ou logs da campanha
2. **Copiar link de tracking** gerado para o usuário teste
3. **O link deve ter formato:** `track.php?rid=XXXXX`

### 4.3 Simular Clique do Usuário
1. **Abrir link em nova aba/navegador:**
   `https://loophish.local/track.php?rid=XXXXX`
2. **Verificar se redireciona** para o sistema de treinamento
3. **Confirmar que abre** `training_player.php` ou página similar

---

## 🎓 **PASSO 5: FAZER O TREINAMENTO**

### 5.1 Assistir ao Conteúdo
1. **Ler todo o conteúdo** do módulo criado
2. **Clicar no botão** "Marcar Vídeo como Assistido"
3. **Procurar botão** "Continuar para Quiz" ou similar

### 5.2 Fazer o Quiz
1. **Responder as 3 perguntas:**
   - Pergunta 1: **Resposta 2** (remetente solicita informações urgentemente)
   - Pergunta 2: **Resposta 3** (deletar e reportar como spam)
   - Pergunta 3: **Falso** (bancos não enviam emails solicitando senhas)

2. **Submeter respostas**
3. **Verificar pontuação** (deve ser 100% = 3/3 corretas)

### 5.3 Concluir Treinamento
1. **Confirmar que passou** (≥ 80% necessário)
2. **Verificar mensagem** de conclusão
3. **Procurar link** "Ver Certificado" ou "Download Certificate"

---

## 🏆 **PASSO 6: VERIFICAR CERTIFICADO**

### 6.1 Acessar Certificado
1. **Clicar no link** do certificado na página de conclusão
2. **OU acessar:** `certificate_view.php?cert_id=XXXXX`
3. **OU ir para:** Interface de gestão de certificados

### 6.2 Validar Certificado
1. **Verificar dados:**
   - ✅ Nome do usuário: "João Silva"  
   - ✅ Módulo: "Segurança Digital - Teste Phishing"
   - ✅ Pontuação: "100%"
   - ✅ Data de conclusão: Data atual
   - ✅ Código de validação: Presente
   
2. **Testar download PDF**
3. **Verificar design** do certificado

### 6.3 Verificar Integração
1. **Ir para:** Sistema de gestão de certificados
2. **Confirmar que certificado** aparece na lista
3. **Verificar dados de analytics** se disponível

---

## ✅ **PASSO 7: VALIDAÇÃO FINAL**

### 7.1 Checklist de Funcionamento
- [ ] ✅ Módulo de treinamento criado com sucesso
- [ ] ✅ Campanha de email configurada com integração  
- [ ] ✅ Link de tracking redireciona para treinamento
- [ ] ✅ Sistema de treinamento carrega corretamente
- [ ] ✅ Quiz funciona e calcula pontuação
- [ ] ✅ Certificado é gerado automaticamente
- [ ] ✅ Certificado pode ser visualizado e baixado
- [ ] ✅ Dados aparecem nos sistemas de gestão

### 7.2 Fluxo Completo Testado
```
📧 Email Phishing → 🖱️ Clique do Usuário → 🎓 Treinamento → 📝 Quiz → 🏆 Certificado
```

### 7.3 Problemas Comuns e Soluções

**Se algum passo falhar:**

1. **Módulo não salva:**
   - Verificar se `tb_training_modules` existe
   - Conferir logs de erro do PHP

2. **Campanha não integra:**
   - Verificar se campo `training_module_id` existe em `tb_core_mailcamp_list`
   - Confirmar configuração de integração

3. **Redirecionamento não funciona:**
   - Verificar `training_redirect.php`
   - Conferir `tb_campaign_training_association`

4. **Quiz não funciona:**
   - Verificar `training_quiz.php`
   - Conferir `tb_training_questions` e `tb_training_quiz_results`

5. **Certificado não gera:**
   - Verificar `certificate_manager.php`
   - Conferir `tb_training_certificates`

---

## 🎯 **RESULTADO ESPERADO**

Ao final deste teste, você deve ter:

1. **✅ Um módulo funcional** de treinamento anti-phishing
2. **✅ Uma campanha** que redireciona usuarios para treinamento
3. **✅ Fluxo completo** funcionando do email ao certificado
4. **✅ Certificado gerado** automaticamente com design profissional
5. **✅ Sistema integrado** com analytics e gestão

**🎉 Se todos os passos funcionarem, o sistema está 100% operacional!**

---

## 📞 **PRÓXIMOS PASSOS**

Após validação bem-sucedida:
1. **Criar módulos reais** para treinamento de clientes
2. **Configurar campanhas de produção** 
3. **Treinar usuários** no sistema
4. **Monitorar resultados** via analytics
5. **Emitir certificados** em escala

**Sistema pronto para uso em produção! 🚀**