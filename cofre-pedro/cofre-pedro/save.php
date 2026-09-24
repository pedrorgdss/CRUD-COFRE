<?php
require __DIR__ . '/config.php'; requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
checkToken();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$service = trim((string) ($_POST['servico'] ?? ''));
$username = trim((string) ($_POST['usuario'] ?? ''));
$secret = (string) ($_POST['senha'] ?? '');
$notes = trim((string) ($_POST['observacoes'] ?? ''));
if ($service === '' || mb_strlen($service) > 120 || $username === '' || mb_strlen($username) > 190 || mb_strlen($notes) > 500 || strlen($secret) > 1024 || (!$id && $secret === '')) {
    http_response_code(422); exit('Dados inválidos. Volte ao formulário e confira os campos.');
}
if ($id) {
    $stmt = db()->prepare('SELECT id FROM credenciais WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) { http_response_code(404); exit('Credencial não encontrada.'); }
    if ($secret !== '') {
        [$cipher, $nonce, $tag] = encryptSecret($secret);
        $stmt = db()->prepare('UPDATE credenciais SET servico=?, usuario_servico=?, senha_cifrada=?, nonce=?, etiqueta=?, observacoes=? WHERE id=? AND usuario_id=?');
        $stmt->execute([$service, $username, $cipher, $nonce, $tag, $notes, $id, $_SESSION['user_id']]);
    } else {
        $stmt = db()->prepare('UPDATE credenciais SET servico=?, usuario_servico=?, observacoes=? WHERE id=? AND usuario_id=?');
        $stmt->execute([$service, $username, $notes, $id, $_SESSION['user_id']]);
    }
    flash('Credencial atualizada.');
} else {
    [$cipher, $nonce, $tag] = encryptSecret($secret);
    $stmt = db()->prepare('INSERT INTO credenciais (usuario_id, servico, usuario_servico, senha_cifrada, nonce, etiqueta, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $service, $username, $cipher, $nonce, $tag, $notes]);
    flash('Credencial cadastrada.');
}
redirect('index.php');
