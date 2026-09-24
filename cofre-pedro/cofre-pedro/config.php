<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'cofre_pedro';
const DB_USER = 'root';
const DB_PASS = ''; // Ajuste conforme seu MySQL local.

if (!extension_loaded('openssl') || !extension_loaded('pdo_mysql')) {
    http_response_code(500);
    exit('Ative as extensões PHP openssl e pdo_mysql.');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

function db(): PDO {
    static $pdo;
    if (!isset($pdo)) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Não foi possível conectar ao banco. Confira config.php e importe banco.sql.');
        }
    }
    return $pdo;
}

function h(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path); exit; }
function token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function checkToken(): void {
    if (!hash_equals(token(), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Formulário inválido. Volte à página e tente novamente.');
    }
}
function requireLogin(): void {
    if (empty($_SESSION['user_id']) || empty($_SESSION['vault_key'])) redirect('login.php');
}
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = [$message, $type]; }
function vaultKey(string $master, string $salt): string {
    return hash_pbkdf2('sha256', $master, $salt, 210000, 32, true);
}
function encryptSecret(string $secret): array {
    $nonce = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($secret, 'aes-256-gcm', $_SESSION['vault_key'], OPENSSL_RAW_DATA, $nonce, $tag);
    if ($cipher === false) throw new RuntimeException('Não foi possível proteger a senha.');
    return [base64_encode($cipher), $nonce, $tag];
}
function decryptSecret(array $row): string {
    $cipher = base64_decode($row['senha_cifrada'], true);
    if ($cipher === false) throw new RuntimeException('Dados inválidos.');
    $secret = openssl_decrypt($cipher, 'aes-256-gcm', $_SESSION['vault_key'], OPENSSL_RAW_DATA, $row['nonce'], $row['etiqueta']);
    if ($secret === false) throw new RuntimeException('Não foi possível abrir a senha.');
    return $secret;
}
