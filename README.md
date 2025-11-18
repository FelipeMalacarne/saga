# SAGA Pattern Coreografado - Transações Distribuídas

Este projeto demonstra a implementação do padrão SAGA coreografado usando Laravel para gerenciar transferências entre carteiras digitais de forma distribuída e confiável.

## 📋 Sumário

- [Como Usar o Projeto](#como-usar-o-projeto)
- [O que é SAGA Pattern?](#o-que-é-saga-pattern)
- [Arquitetura da Solução](#arquitetura-da-solução)
- [Implementação Passo a Passo](#implementação-passo-a-passo)
  - [1. Eventos do Sistema](#1-eventos-do-sistema)
  - [2. Listeners (Orquestradores)](#2-listeners-orquestradores)
  - [3. Fluxo de Sucesso](#3-fluxo-de-sucesso)
  - [4. Fluxo de Compensação](#4-fluxo-de-compensação)
- [Testando a Aplicação](#testando-a-aplicação)

---

## 🚀 Como Usar o Projeto

### Pré-requisitos
- Docker e Docker Compose instalados
- Git

### Passos para Executar

1. **Clone o repositório:**
```bash
git clone https://github.com/FelipeMalacarne/saga
cd saga
```

2. **Inicie a aplicação:**
```bash
docker compose up
```

> ⚠️ **Nota:** Todo o setup (instalação de dependências, migrations, etc.) é feito automaticamente no build da imagem Docker.

3. **Acesse a aplicação:**
A aplicação estará disponível em `http://localhost:80`

---

## 🧩 O que é SAGA Pattern?

O **SAGA Pattern** é um padrão de design para gerenciar transações distribuídas em arquiteturas de microserviços. Em vez de usar transações ACID tradicionais (que não funcionam bem em sistemas distribuídos), o SAGA divide uma transação grande em várias transações menores e locais.

### Tipos de SAGA:

1. **Coreografada** (usada neste projeto): Cada serviço publica eventos e reage a eventos de outros serviços
2. **Orquestrada**: Um orquestrador central coordena todas as etapas

### Por que usar SAGA?

- ✅ Mantém consistência eventual em sistemas distribuídos
- ✅ Cada serviço mantém sua própria base de dados
- ✅ Permite rollback através de transações compensatórias
- ✅ Não bloqueia recursos por longos períodos

---

## 🏗️ Arquitetura da Solução

Nossa aplicação simula uma **transferência bancária entre carteiras** com validação antifraude:

```
Carteira A (origem) → Transferência → Validação Antifraude → Carteira B (destino)
```

### Fluxo Normal (Happy Path):
```
TransferRequested → FundsReserved → AntiFraudApproved → TransferSettled
```

### Fluxo de Compensação (Unhappy Path):
```
TransferRequested → FundsReserved → AntiFraudRejected → ReservationReleased
```

---

## 📝 Implementação Passo a Passo

### 1. Eventos do Sistema

Os eventos representam **fatos que já aconteceram** no sistema. Eles são a base da comunicação na SAGA coreografada.

#### 1.1. `TransferRequested`
**Por que existe?** É o evento inicial que inicia toda a saga quando um usuário solicita uma transferência.

```php
// Disparado em: TransferController::create()
// Dados: transfer_id, from_wallet_id, to_wallet_id, amount
```

#### 1.2. `FundsReserved`
**Por que existe?** Confirma que o dinheiro foi reservado (bloqueado) na carteira de origem, garantindo que está disponível.

```php
// Disparado após: Verificação de saldo suficiente
// Próximo passo: Validação antifraude
```

#### 1.3. `AntiFraudApproved`
**Por que existe?** Indica que a transferência passou pela validação de segurança e pode ser concluída.

```php
// Disparado após: Análise antifraude bem-sucedida
// Próximo passo: Finalizar transferência
```

#### 1.4. `AntiFraudRejected`
**Por que existe?** Indica que a transferência foi bloqueada por segurança, iniciando o processo de compensação.

```php
// Disparado quando: Transferência considerada suspeita
// Próximo passo: Desfazer reserva de fundos
```

#### 1.5. `ReservationReleased`
**Por que existe?** Confirma que os fundos bloqueados foram liberados após uma rejeição.

```php
// Transação compensatória: Desfaz a reserva
```

#### 1.6. `TransferSettled`
**Por que existe?** Marca o fim bem-sucedido da saga - a transferência foi concluída.

```php
// Estado final: Dinheiro transferido e transferência finalizada
```

---

### 2. Listeners (Orquestradores)

Os Listeners são os **processadores de eventos** que executam a lógica de negócio.

#### 2.1. `ReserveFunds`
**Ouve:** `TransferRequested`  
**Faz:**
- Verifica se a carteira de origem tem saldo suficiente
- Reserva (bloqueia) o valor na carteira de origem
- Dispara `FundsReserved` se bem-sucedido

**Por que é necessário?**  
Garante atomicidade da reserva de fundos antes de prosseguir com validações.

#### 2.2. `AntiFraudCheck`
**Ouve:** `FundsReserved`  
**Faz:**
- Executa verificações de segurança (valores suspeitos, limites, padrões)
- Dispara `AntiFraudApproved` ou `AntiFraudRejected`

**Por que é necessário?**  
Adiciona camada de segurança antes de concluir transferências.

#### 2.3. `SettleTransfer`
**Ouve:** `AntiFraudApproved`  
**Faz:**
- Debita da carteira de origem
- Credita na carteira de destino
- Marca transferência como concluída
- Dispara `TransferSettled`

**Por que é necessário?**  
Executa a transferência efetiva dos fundos após todas as validações.

#### 2.4. `ReleaseFunds`
**Ouve:** `AntiFraudRejected`  
**Faz:**
- Libera a reserva na carteira de origem
- Marca transferência como rejeitada
- Dispara `ReservationReleased`

**Por que é necessário?**  
**Transação compensatória** - desfaz a reserva quando a saga falha.

---

### 3. Fluxo de Sucesso

```mermaid
TransferRequested
    ↓
[ReserveFunds Listener]
    ↓
FundsReserved
    ↓
[AntiFraudCheck Listener]
    ↓
AntiFraudApproved
    ↓
[SettleTransfer Listener]
    ↓
TransferSettled ✅
```

**Estados da Transferência:**
1. `REQUESTED` → Solicitada
2. `RESERVED` → Fundos reservados
3. `APPROVED` → Aprovada pela antifraude
4. `SETTLED` → Concluída

---

### 4. Fluxo de Compensação

Quando algo dá errado, o sistema **compensa** as ações já realizadas:

```mermaid
TransferRequested
    ↓
[ReserveFunds Listener]
    ↓
FundsReserved
    ↓
[AntiFraudCheck Listener]
    ↓
AntiFraudRejected ❌
    ↓
[ReleaseFunds Listener] (Compensação)
    ↓
ReservationReleased
```

**Estados da Transferência:**
1. `REQUESTED` → Solicitada
2. `RESERVED` → Fundos reservados
3. `REJECTED` → Rejeitada
4. Reserva liberada (fundos devolvidos)

---

## 🧪 Testando a Aplicação

### Criar uma Transferência

```bash
curl -X POST http://localhost:8000/api/transfer
```

**Resposta:**
```json
{
  "tx_id": "uuid-da-transferencia"
}
```

### O que Acontece Internamente:

1. **TransferController** cria:
   - Carteira A com R$ 5.000
   - Carteira B com R$ 3.000
   - Transferência de R$ 450 (A → B)

2. **Eventos são disparados em cadeia**:
   - Sistema reserva R$ 450 da Carteira A
   - Validação antifraude analisa a operação
   - Se aprovado: transfere fundos
   - Se rejeitado: libera reserva

3. **Resultado Final** (sucesso):
   - Carteira A: R$ 4.550
   - Carteira B: R$ 3.450
   - Status: `SETTLED`

---

## 🎓 Conceitos-Chave Aprendidos

1. **Eventos como Fatos Imutáveis:** Cada evento representa algo que JÁ aconteceu
2. **Eventual Consistency:** O sistema alcança consistência ao longo do tempo
3. **Compensação:** Transações que desfazem operações anteriores
4. **Desacoplamento:** Serviços não se conhecem, apenas reagem a eventos
5. **Rastreabilidade:** Cada etapa da saga é registrada

---

## 📚 Próximos Passos

- Adicionar retry logic para falhas temporárias
- Implementar timeout para etapas da saga
- Adicionar observabilidade (logs, métricas)
- Criar testes automatizados para todos os fluxos
- Implementar circuit breaker para serviços externos

---

## 🤝 Contribuindo

Este é um projeto educacional. Sinta-se livre para experimentar e modificar!
