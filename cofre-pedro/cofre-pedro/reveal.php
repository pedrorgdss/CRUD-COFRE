<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(json_encode(['erro' => 'Método inválido.'])); }
if (empty($_SESSION['user_id']) || empty($_SESSION['vault_key'])) { http_response_code(401); exit(json_encode(['erro' => 'Sessão expirada.'])); }
checkToken();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit(json_encode(['erro' => 'ID inválido.'])); }
$stmt = db()->prepare('SELECT senha_cifrada, nonce, etiqueta FROM credenciais WHERE id = ? AND usuario_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]); $row = $stmt->fetch();
if (!$row) { http_response_code(404); exit(json_encode(['erro' => 'Credencial não encontrada.'])); }
try { echo json_encode(['senha' => decryptSecret($row)], JSON_INVALID_UTF8_SUBSTITUTE); }
catch (RuntimeException $e) { http_response_code(500); echo json_encode(['erro' => $e->getMessage()]); }
