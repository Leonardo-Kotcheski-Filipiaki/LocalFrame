# 🚀 A Local Frame

> Um framework PHP minimalista e em construção, criado do zero para explorar o potencial do **PHP 8.4 puro**.

![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)
![PHP Version](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)

## 📌 Sobre o Projeto

O **A Local Frame** é um laboratório de desenvolvimento criado por [Leonardo Kotcheski Filipiaki](https://github.com/Leonardo-Kotcheski-Filipiaki). O objetivo principal deste projeto é testar e aprofundar conhecimentos sobre a arquitetura interna de um framework web, utilizando apenas o **PHP 8.4 nativo**, sem dependências de frameworks terceiros.

Atualmente, o projeto executa de forma 100% local e serve como base para o estudo de roteamento, fluxo de requisição/resposta e abstrações da linguagem.

---

## ⚙️ Estado Atual e Roadmap

### 🟢 Funcionalidades Atuais
* [x] Estrutura base para execução em ambiente local.
* [x] Ponto de entrada centralizado e ciclo de vida básico da requisição.
* [x] Implementação utilizando syntax e recursos do PHP 8.4.

### 🟡 Próximos Passos (Em Breve)
* [ ] **Camada de Banco de Dados:** Criação de uma abstração de conexão via PDO / Query Builder simplificado.
* [ ] **Sistema de Roteamento Dinâmico:** Suporte avançado a parâmetros de URL e métodos HTTP.
* [ ] **Renderização de Views:** Mecanismo simples de suporte a templates.
* [ ] **Tratamento de Exceções/Erros:** Handlers visuais para facilidade de depuração.

---

## 🛠️ Requisitos

* **PHP >= 8.4**
* Servidor web local (ou o próprio servidor embutido do PHP)
* Extensão **PDO** ativa (necessária para as futuras atualizações de banco de dados)

---

## 🚀 Como Executar o Projeto Localmente

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/Leonardo-Kotcheski-Filipiaki/A-Local-Frame.git](https://github.com/Leonardo-Kotcheski-Filipiaki/A-Local-Frame.git)
   cd A-Local-Frame
