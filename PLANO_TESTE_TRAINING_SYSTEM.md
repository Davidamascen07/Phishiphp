# 🧪 Plano de Teste Completo - LooPhish V2.0 Sistema de Treinamento

## 📋 Visão Geral do Plano

Este documento apresenta um plano de teste abrangente para validar as funcionalidades implementadas na **Fase 1** do sistema de treinamento integrado do LooPhish V2.0.

### 🎯 Objetivos dos Testes
- Validar os 3 fluxos modulares de treinamento implementados
- Verificar integração entre campanhas de phishing e módulos de treinamento
- Testar funcionalidades de quiz, certificação e gamificação
- Garantir a qualidade e funcionalidade do sistema antes da produção

---

## 🔧 Pré-requisitos para Execução dos Testes

### ✅ Ambiente de Teste
- [ ] Servidor web com PHP 7.4+ e MySQL 5.7+
- [ ] LooPhish V2.0 instalado e funcionando
- [ ] Base de dados configurada com tabelas de treinamento
- [ ] Pelo menos 3 usuários de teste criados
- [ ] Navegadores modernos para testes de frontend (Chrome, Firefox, Edge)

### ✅ Arquivos Implementados Necessários
- [ ] `training_integration_manager.php` - API backend central
- [ ] `training_player.php` - Player de vídeo com tracking
- [ ] `training_quiz.php` - Sistema de quiz interativo
- [ ] `training_completion.php` - Página de conclusão e certificados
- [ ] `training_redirect.php` - Sistema de redirecionamento automático
- [ ] `training_integration.js` - Integração JavaScript frontend
- [ ] `TrainingManagement.php` - Interface de gestão de treinamentos

### ✅ Dados de Teste Preparados
- [ ] 3 módulos de treinamento com diferentes tipos (video, quiz, mixed)
- [ ] 2 campanhas de phishing ativas para integração
- [ ] URLs de vídeo válidas (YouTube, Vimeo, ou locais)
- [ ] Perguntas de quiz configuradas nos módulos
- [ ] Usuários com diferentes perfis de acesso

---

## 🧩 Matriz de Teste por Funcionalidade

### 1️⃣ **FLUXO 1: Email → Link → Video**

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T1.1 | **Configuração básica do fluxo** | 1. Criar módulo tipo "video"<br>2. Configurar URL de vídeo<br>3. Integrar com campanha de phishing | Módulo criado e vinculado à campanha | ⏳ |
| T1.2 | **Interação do usuário com email** | 1. Usuário recebe email de phishing<br>2. Clica no link malicioso<br>3. Sistema detecta clique | Click registrado no sistema | ⏳ |
| T1.3 | **Redirecionamento automático** | 1. Após click no link<br>2. Sistema identifica usuário<br>3. Redireciona para training_player.php | Redirecionamento para vídeo de treinamento | ⏳ |
| T1.4 | **Reprodução de vídeo** | 1. Player carrega vídeo<br>2. Usuário assiste ao conteúdo<br>3. Progress tracking funciona | Vídeo reproduz e progresso é monitorado | ⏳ |
| T1.5 | **Conclusão do treinamento** | 1. Vídeo termina ou atinge 80%<br>2. Sistema marca como completo<br>3. Redireciona para página de conclusão | Treinamento marcado como concluído | ⏳ |

### 2️⃣ **FLUXO 2: Email → Link → Fake Page → Video**

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T2.1 | **Configuração do fluxo com página falsa** | 1. Criar campanha com página falsa<br>2. Configurar módulo de treinamento<br>3. Integrar fluxo completo | Fluxo configurado corretamente | ⏳ |
| T2.2 | **Interação com página falsa** | 1. Usuário acessa página falsa<br>2. Preenche formulário ou interage<br>3. Sistema captura dados | Interação registrada e dados coletados | ⏳ |
| T2.3 | **Ativação do treinamento** | 1. Após submissão na página falsa<br>2. training_integration.js detecta evento<br>3. Redireciona para treinamento | Redirecionamento automático para vídeo | ⏳ |
| T2.4 | **Experiência educacional** | 1. Vídeo explicativo carrega<br>2. Mostra warning sobre phishing<br>3. Usuário completa treinamento | Usuário educado sobre riscos de phishing | ⏳ |

### 3️⃣ **FLUXO 3: Email → Link → Fake Page → Video → Quiz** (Completo)

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T3.1 | **Configuração completa do fluxo** | 1. Criar módulo tipo "mixed"<br>2. Configurar vídeo + quiz<br>3. Definir perguntas e pontuação | Módulo completo configurado | ⏳ |
| T3.2 | **Fluxo completo do usuário** | 1. Email → Click → Página falsa<br>2. Submissão → Vídeo<br>3. Vídeo → Quiz automático | Transições fluidas entre etapas | ⏳ |
| T3.3 | **Sistema de quiz interativo** | 1. Quiz carrega após vídeo<br>2. Timer de 10 minutos funciona<br>3. Perguntas são apresentadas | Quiz funcional com timer | ⏳ |
| T3.4 | **Avaliação e pontuação** | 1. Usuário responde perguntas<br>2. Sistema calcula pontuação<br>3. Feedback visual é apresentado | Pontuação calculada corretamente | ⏳ |
| T3.5 | **Emissão de certificado** | 1. Pontuação ≥ 70% (configurável)<br>2. Sistema gera certificado<br>3. Usuário pode baixar PDF | Certificado emitido e disponível | ⏳ |

---

## 🎮 Testes de Gamificação e Ranking

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T4.1 | **Sistema de pontos** | 1. Usuário completa treinamento<br>2. Pontos são atribuídos<br>3. Total acumulado atualizado | Pontuação correta no perfil | ⏳ |
| T4.2 | **Ranking de usuários** | 1. Múltiplos usuários completam<br>2. Sistema calcula rankings<br>3. Leaderboard é atualizado | Ranking correto e atualizado | ⏳ |
| T4.3 | **Certificados únicos** | 1. Cada certificado tem ID único<br>2. Código de validação gerado<br>3. Histórico mantido | Certificados únicos e rastreáveis | ⏳ |

---

## 🔗 Testes de Integração

### 📊 **TrackerGenerator Integration**

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T5.1 | **Seleção de módulo no TrackerGenerator** | 1. Abrir TrackerGenerator<br>2. Ver opção de treinamento<br>3. Selecionar módulo específico | Módulos listados corretamente | ⏳ |
| T5.2 | **Geração de URL com treinamento** | 1. Configurar tracker com treinamento<br>2. Gerar URL<br>3. Testar link gerado | URL contém parâmetros de treinamento | ⏳ |
| T5.3 | **Tracking de clicks com training** | 1. Click em link gerado<br>2. Verificar logs do sistema<br>3. Validar dados coletados | Click registrado com contexto de treinamento | ⏳ |

### 🎯 **Campaign Integration**

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| T6.1 | **Atribuição automática** | 1. Usuário falha em teste de phishing<br>2. Sistema atribui treinamento<br>3. Notificação é enviada | Treinamento atribuído automaticamente | ⏳ |
| T6.2 | **Dashboard de acompanhamento** | 1. Visualizar campanhas ativas<br>2. Ver estatísticas de treinamento<br>3. Monitorar progresso | Dashboard mostra dados corretos | ⏳ |

---

## 🌐 Testes de Compatibilidade

### 💻 **Browsers & Devices**

| Ambiente | Teste | Resultado Esperado | Status |
|----------|-------|-------------------|---------|
| Chrome 120+ | Fluxo completo | Funciona perfeitamente | ⏳ |
| Firefox 120+ | Player de vídeo | Reprodução sem problemas | ⏳ |
| Edge 120+ | Quiz interativo | Interface responsiva | ⏳ |
| Mobile Safari | Vídeos responsivos | Adaptação mobile | ⏳ |
| Mobile Chrome | Touch interactions | Funcionalidade touch | ⏳ |

### 🎥 **Video Sources**

| Tipo de Vídeo | Teste | Resultado Esperado | Status |
|---------------|-------|-------------------|---------|
| YouTube | Embed e tracking | Player integrado funciona | ⏳ |
| Vimeo | Progress monitoring | Progresso monitorado | ⏳ |
| Local MP4 | HTML5 player | Reprodução local fluida | ⏳ |
| URLs externos | Fallback handling | Graceful degradation | ⏳ |

---

## 🔒 Testes de Segurança

| ID | Cenário de Teste | Passos | Resultado Esperado | Status |
|----|------------------|--------|-------------------|---------|
| S1.1 | **Autenticação de usuário** | 1. Tentar acessar sem login<br>2. Verificar redirecionamento<br>3. Validar sessão | Acesso negado sem autenticação | ⏳ |
| S1.2 | **Validação de parâmetros** | 1. Enviar parâmetros inválidos<br>2. Testar SQL injection<br>3. Validar sanitização | Parâmetros validados e sanitizados | ⏳ |
| S1.3 | **CSRF Protection** | 1. Tentar requests cross-origin<br>2. Verificar tokens CSRF<br>3. Validar headers | Proteção CSRF funcionando | ⏳ |

---

## 📈 Testes de Performance

| ID | Cenário de Teste | Métrica | Resultado Esperado | Status |
|----|------------------|---------|-------------------|---------|
| P1.1 | **Carregamento inicial** | Time to first byte | < 2 segundos | ⏳ |
| P1.2 | **Player de vídeo** | Tempo de inicialização | < 3 segundos | ⏳ |
| P1.3 | **Quiz loading** | Renderização completa | < 1 segundo | ⏳ |
| P1.4 | **Database queries** | Tempo de resposta | < 500ms por query | ⏳ |

---

## 🚨 Testes de Cenários de Erro

| ID | Cenário de Teste | Condição de Erro | Comportamento Esperado | Status |
|----|------------------|------------------|----------------------|---------|
| E1.1 | **Vídeo indisponível** | URL de vídeo inválida | Mensagem de erro elegante | ⏳ |
| E1.2 | **Timeout de quiz** | Timer expira | Submissão automática e feedback | ⏳ |
| E1.3 | **Conexão perdida** | Internet falha | Recuperação automática | ⏳ |
| E1.4 | **Certificado falha** | Erro na geração | Retry automático | ⏳ |

---

## 📊 Relatórios e Validação de Dados

### 🎯 **Training Analytics**

| Métrica | Teste | Validação | Status |
|---------|-------|-----------|---------|
| Taxa de conclusão | % usuários que completam | > 80% completion rate | ⏳ |
| Tempo médio | Duração dos treinamentos | Dentro do estimado | ⏳ |
| Pontuação média | Scores dos quizzes | Distribuição normal | ⏳ |
| Certificados emitidos | Quantidade vs. conclusões | 1:1 ratio | ⏳ |

### 📋 **Data Integrity**

| Dados | Validação | Critério | Status |
|-------|-----------|-----------|---------|
| Progresso de usuário | Consistência temporal | Timestamps lógicos | ⏳ |
| Pontuações de quiz | Cálculo correto | Fórmula matemática | ⏳ |
| IDs de certificado | Unicidade | Sem duplicatas | ⏳ |
| Logs de atividade | Completude | Todos eventos registrados | ⏳ |

---

## 🎯 Critérios de Aceitação

### ✅ **Must Have (Críticos)**
- [ ] Todos os 3 fluxos funcionando completamente
- [ ] Sistema de quiz operacional com timer
- [ ] Certificados sendo gerados corretamente
- [ ] Integração com TrackerGenerator funcionando
- [ ] Dados sendo persistidos corretamente
- [ ] Interface responsiva em todos os devices

### ✅ **Should Have (Importantes)**
- [ ] Performance dentro dos parâmetros
- [ ] Gamificação e ranking funcionais
- [ ] Relatórios de analytics corretos
- [ ] Compatibilidade cross-browser
- [ ] Tratamento elegante de erros

### ✅ **Could Have (Desejáveis)**
- [ ] Animações e transições suaves
- [ ] Notificações em tempo real
- [ ] Exportação de relatórios avançados
- [ ] Personalização de certificados

---

## 🔄 Processo de Execução dos Testes

### 📅 **Fase 1: Testes Unitários** (1-2 dias)
1. Testar cada arquivo PHP individualmente
2. Validar todas as funções JavaScript
3. Verificar integridade do banco de dados
4. Executar testes de segurança básicos

### 📅 **Fase 2: Testes de Integração** (2-3 dias)
1. Testar os 3 fluxos modulares completos
2. Validar integração com campanhas existentes
3. Verificar TrackerGenerator integration
4. Testar cenários de usuário real

### 📅 **Fase 3: Testes de Sistema** (1-2 dias)
1. Testes de performance e carga
2. Compatibilidade cross-browser
3. Testes de usuário final (UAT)
4. Validação de relatórios e analytics

### 📅 **Fase 4: Testes de Produção** (1 dia)
1. Deploy em ambiente de staging
2. Testes finais com dados reais
3. Validação de backup e recovery
4. Go-live checklist

---

## 📝 Template de Execução de Teste

```
TESTE ID: [T1.1]
TESTADOR: [Nome]
DATA: [DD/MM/YYYY]
AMBIENTE: [Development/Staging/Production]

PRÉ-CONDIÇÕES:
- [ ] Sistema funcionando
- [ ] Dados de teste preparados
- [ ] Usuário de teste criado

PASSOS EXECUTADOS:
1. [Passo 1 detalhado]
2. [Passo 2 detalhado]
3. [Passo 3 detalhado]

RESULTADO OBTIDO:
[Descrição do que aconteceu]

STATUS: [✅ PASSOU | ❌ FALHOU | ⚠️ BLOQUEADO]

EVIDÊNCIAS:
- Screenshot: [caminho/arquivo]
- Log: [mensagem de erro se houver]
- Video: [se aplicável]

OBSERVAÇÕES:
[Qualquer observação adicional]
```

---

## 🏁 Checklist Final de Aceitação

### ✅ **Funcionalidades Core**
- [ ] Email → Link → Video (Fluxo 1) - 100% funcional
- [ ] Email → Link → Fake Page → Video (Fluxo 2) - 100% funcional  
- [ ] Email → Link → Fake Page → Video → Quiz (Fluxo 3) - 100% funcional
- [ ] Sistema de pontuação e ranking operacional
- [ ] Emissão de certificados automática
- [ ] Interface de gestão TrainingManagement.php funcional

### ✅ **Qualidade e Performance**
- [ ] Todos os testes de compatibilidade passaram
- [ ] Performance dentro dos SLAs definidos
- [ ] Segurança validada (OWASP Top 10)
- [ ] Dados íntegros e consistentes
- [ ] Tratamento de erros implementado

### ✅ **Documentação e Entrega**
- [ ] Manual de usuário criado
- [ ] Documentação técnica atualizada
- [ ] Relatório de testes completo
- [ ] Plano de rollback preparado
- [ ] Training para administradores realizado

---

**📊 Status Geral do Plano: 0% Concluído**
**🎯 Meta: 100% dos testes críticos passando antes do go-live**
**📅 Prazo Estimado: 5-7 dias úteis para execução completa**

---

*Documento criado em: 5 de outubro de 2025*  
*Versão: 1.0*  
*Responsável: Sistema LooPhish V2.0*