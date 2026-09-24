# Cofre 

Atividade de **Pedro Rodrigues Pimentel (RM 26044)**: Gerenciador de Senhas (Cofre Local). CRUD de serviços, usuários e senhas, com busca e observações. Construído com PHP, Bootstrap, JavaScript e MySQL.

## Como instalar

1. Use PHP 8.1+ com extensões `pdo_mysql`, `openssl` e `mbstring`, além de MySQL 8+ ou MariaDB compatível.
2. Importe **`banco.sql`** no phpMyAdmin (aba Importar) ou execute `mysql -u root -p < banco.sql`.
3. Ajuste usuário e senha do MySQL em **`config.php`**.
4. Coloque esta pasta em `htdocs` (XAMPP) e acesse `http://localhost/cofre-pedro/register.php`. Como alternativa, na pasta do projeto execute `php -S localhost:8000` e acesse `http://localhost:8000/register.php`.
5. Crie uma conta, entre e cadastre uma credencial. Teste buscar, mostrar, copiar, editar e excluir.

## Funcionamento

As senhas dos serviços são cifradas antes de serem gravadas no MySQL, usando AES-256-GCM. A chave é derivada da senha mestra com PBKDF2 e um sal individual e fica apenas na sessão PHP enquanto o usuário está conectado. A senha mestra é armazenada como hash (`password_hash`), não em texto aberto. Cada consulta e alteração restringe os registros ao usuário conectado. Há consultas parametrizadas, proteção CSRF e escape da saída HTML.

**Atenção:** sem a senha mestra, as credenciais existentes não podem ser recuperadas. Este é um projeto escolar demonstrativo; para guardar senhas reais, prefira um gerenciador de senhas auditado. O Bootstrap é carregado por CDN e requer internet para a estilização completa.

## Arquivos

- `banco.sql`: criação do banco e das tabelas.
- `config.php`: conexão, sessão, segurança e criptografia.
- `register.php`, `login.php`, `logout.php`: acesso ao cofre.
- `index.php`, `form.php`, `save.php`, `delete.php`, `reveal.php`: CRUD e visualização.
- `assets/`: estilos e interações em JavaScript.
