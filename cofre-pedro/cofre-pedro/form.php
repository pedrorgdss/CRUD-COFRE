<?php
require __DIR__ . '/config.php'; requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (isset($_GET['id']) && !$id) { http_response_code(404); exit('Credencial não encontrada.'); }
$item = null;
if ($id) {
    $stmt = db()->prepare('SELECT id, servico, usuario_servico, observacoes FROM credenciais WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $_SESSION['user_id']]); $item = $stmt->fetch();
    if (!$item) { http_response_code(404); exit('Credencial não encontrada.'); }
}
$title = $item ? 'Editar credencial' : 'Nova credencial'; require __DIR__ . '/partials/header.php';
?>
<main class="container py-5 form-page"><a href="index.php" class="text-secondary text-decoration-none">← Voltar ao cofre</a><h1 class="fw-bold mt-3 mb-1"><?= h($title) ?></h1><p class="text-secondary mb-4">Preencha os dados do serviço. <?= $item ? 'Deixe a senha vazia para manter a atual.' : 'A senha será protegida antes de ir para o banco.' ?></p>
<div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5"><form action="save.php" method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
<div class="mb-3"><label for="servico" class="form-label">Serviço *</label><input class="form-control" id="servico" name="servico" maxlength="120" required value="<?= h($item['servico'] ?? '') ?>" placeholder="Ex.: E-mail, rede social"></div>
<div class="mb-3"><label for="usuario" class="form-label">Usuário ou e-mail *</label><input class="form-control" id="usuario" name="usuario" maxlength="190" required value="<?= h($item['usuario_servico'] ?? '') ?>" autocomplete="off"></div>
<div class="mb-3"><label for="senha" class="form-label"><?= $item ? 'Nova senha (opcional)' : 'Senha *' ?></label><div class="input-group"><input class="form-control" type="password" id="senha" name="senha" <?= $item ? '' : 'required' ?> autocomplete="new-password" maxlength="1024"><button type="button" class="btn btn-outline-secondary toggle-password" data-target="senha">Mostrar</button><button type="button" class="btn btn-outline-secondary generate-password" data-target="senha">Gerar</button></div></div>
<div class="mb-4"><label for="observacoes" class="form-label">Observações</label><textarea class="form-control" id="observacoes" name="observacoes" maxlength="500" rows="3" placeholder="Informações opcionais sobre a conta"><?= h($item['observacoes'] ?? '') ?></textarea></div>
<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Salvar credencial</button><a class="btn btn-outline-secondary" href="index.php">Cancelar</a></div></form></div></div></main>
<?php require __DIR__ . '/partials/footer.php'; ?>
