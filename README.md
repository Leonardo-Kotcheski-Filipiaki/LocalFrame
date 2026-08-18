# 🚀 LocalFrame

> Um framework PHP minimalista e em construção, criado do zero para explorar o potencial do **PHP 8.4 puro**.

![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)
![PHP Version](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)

## 📌 Sobre o Projeto

O **LocalFrame** é um laboratório de desenvolvimento criado por mim com o objetivo principal de testar e aprofundar conhecimentos sobre a arquitetura interna de um framework web, utilizando apenas **PHP 8.4 nativo**, sem dependências de frameworks terceiros, tendo de criar funcionalidades do zero, fazendo uso também do apoio de IA (Gemini)

Atualmente, o projeto executa de forma 100% local e serve como base para o estudo de roteamento, fluxo de requisição/resposta e abstrações da linguagem.

---

## ⚙️ Estado Atual e Roadmap

### 🟢 Funcionalidades Atuais
* [x] Estrutura base para execução em ambiente local, com database local em arquivos.txt
* [x] Ponto de entrada centralizado e ciclo de vida básico da requisição.
* [x] Implementação utilizando syntax e recursos do PHP 8.4.
* [x] Template engine baseada em como funciona no **Laravel**.
* [x] Exception Handler para melhor visualização do erro.

### 🟡 Próximos Passos (Em Breve)
* [ ] **Camada de Banco de Dados:** Criação de conexão com banco de dados, habilitando persistência automatica com TPT e anotações/atributos do php.
* [ ] **Aprimoramento de Tratamento de Exceções/Erros:**: Aprimorar o Exception Handler para uma visualização mais harmonica e inteligênte. 

---

## 🛠️ Requisitos

* **PHP >= 8.4**
* Servidor web local (ou o próprio servidor embutido do PHP)
* 
---

## 🚀 Como Executar o Projeto Localmente

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/Leonardo-Kotcheski-Filipiaki/LocalFrame.git](https://github.com/Leonardo-Kotcheski-Filipiaki/LocalFrame.git)
   cd LocalFrame
2. **Iniciar o servidor embutido no PHP via terminal**
   ```bash
   php -S localhost:8000 -t public
3. **Acessar navegador**
   + Pode acessar o navegador via
   ```plaintext
   localhost:8000

LocalFrame/
   ├── 📁 App/                         # Camada da Aplicação (código do projeto)
   │   ├── 📁 Classes/                 # Regras de negócio e entidades (camada equivalente às Models)
   │   ├── 📁 Configurations/          # Arquivos de configuração da aplicação
   │   ├── 📁 Controllers/             # Controladores que gerenciam o fluxo de requisição e resposta
   │   ├── 📁 database/                # Armazenamento do banco de dados local e migrações
   │   ├── 📁 DTOs/                    # Data Transfer Objects (transportadores de dados tipados)
   │   ├── 📁 Enums/                   # Enumeradores tipados da aplicação
   │   └── 📁 Views/                   # Camada de apresentação (templates e arquivos de interface)
   │
   ├── 📁 Core/                        # Núcleo e engrenagens do Framework
   │   ├── 📁 Bases/                   # Classes base e contratos abstratos para Controllers e Classes
   │   ├── 📁 Essentials/              # O "motor" do framework (Roteamento, ciclo HTTP, Engine)
   │   ├── 📁 Notations/               # Mapeamento de atributos nativos do PHP (PHP Attributes / Annotations)
   │   └── 📁 Notifications/           # Sistema de notificações, mensagens flash e tratamento de alertas
   │
   ├── 📄 .gitignore                   # Arquivo de regras para ignorar arquivos no versionamento Git
   ├── 📄 _autoload.php                # Autoloader nativo e leve para carregamento dinâmico de classes
   ├── 📄 index.php                    # Front Controller (ponto de entrada único de todas as requisições)
   └── 📄 README.md                    # Documentação técnica do repositório

## 👨‍💻 Autor
Desenvolvido por Leonardo Kotcheski Filipiaki.

GitHub: [@Leonardo Kotcheski Filipiaki](https://github.com/Leonardo-Kotcheski-Filipiaki)
