<?php
require __DIR__ . '/config.php'; requireLogin();
$search = trim((string) ($_GET['q'] ?? ''));
if (mb_strlen($search) > 120) $search = mb_substr($search, 0, 120);
$stmt = db()->prepare('SELECT id, servico, usuario_servico, observacoes, atualizado_em FROM credenciais WHERE usuario_id = :uid AND (servico LIKE :query1 OR usuario_servico LIKE :query2) ORDER BY servico, id DESC');
$stmt->execute(['uid' => $_SESSION['user_id'], 'query1' => '%' . $search . '%', 'query2' => '%' . $search . '%']);
$items = $stmt->fetchAll();
$title = 'Minhas senhas'; require __DIR__ . '/partials/header.php';
?>
<main class="container py-5"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><span class="eyebrow">GERENCIADOR DE SENHAS</span><h1 class="fw-bold mb-1">Minhas credenciais</h1><p class="text-secondary mb-0">Seus serviços organizados em um só lugar.</p></div><a class="btn btn-primary" href="form.php">+ Nova credencial</a></div>
<form class="mb-4" method="get" role="search"><label for="q" class="visually-hidden">Buscar serviço ou usuário</label><div class="input-group"><input id="q" class="form-control" name="q" placeholder="Buscar serviço ou usuário..." value="<?= h($search) ?>"><button class="btn btn-outline-secondary" type="submit">Buscar</button></div></form>
<?php if (!$items): ?><div class="empty-state text-center"><div class="display-5 mb-3">⌕</div><h2 class="h5">Nenhuma credencial encontrada</h2><p class="text-secondary mb-0"><?= $search !== '' ? 'Tente outro termo de busca.' : 'Clique em “Nova credencial” para cadastrar a primeira.' ?></p></div>
<?php else: ?><div class="row g-3"><?php foreach ($items as $item): ?><div class="col-md-6 col-xl-4"><article class="card h-100 border-0 shadow-sm"><div class="card-body p-4"><div class="d-flex align-items-start gap-3 mb-3"><div class="service-icon"><?= h(mb_strtoupper(mb_substr($item['servico'], 0, 1))) ?></div><div class="min-width-0"><h2 class="h5 fw-bold mb-1 text-truncate"><?= h($item['servico']) ?></h2><div class="text-secondary small text-break"><?= h($item['usuario_servico']) ?></div></div></div>
<p class="text-secondary small mb-3 clamp"><?= $item['observacoes'] !== null && $item['observacoes'] !== '' ? h($item['observacoes']) : 'Sem observações' ?></p>
<div class="d-flex flex-wrap gap-2"><button class="btn btn-sm btn-outline-primary reveal-btn" type="button" data-id="<?= (int) $item['id'] ?>">Mostrar senha</button><a href="form.php?id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a><form method="post" action="delete.php" class="ms-auto delete-form"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form></div>
<div class="secret-box mt-3 d-none" aria-live="polite"><code class="secret-value text-break"></code><button type="button" class="btn btn-sm btn-link copy-btn">Copiar</button></div>
</div></article></div><?php endforeach; ?></div><?php endif; ?></main>
<script>window.vaultToken = <?= json_encode(token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
