<?php
require __DIR__ . '/config.php'; requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
checkToken();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Identificador inválido.'); }
$stmt = db()->prepare('DELETE FROM credenciais WHERE id = ? AND usuario_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
flash($stmt->rowCount() ? 'Credencial excluída.' : 'Credencial não encontrada.', $stmt->rowCount() ? 'success' : 'warning');
redirect('index.php');
