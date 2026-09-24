<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="referrer" content="no-referrer">
<title><?= h($title ?? 'Cofre') ?> · Cofre</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="navbar navbar-expand navbar-dark"><div class="container"><a class="navbar-brand fw-bold" href="index.php">✦ Cofre</a>
<?php if (!empty($_SESSION['user_id'])): ?><div class="ms-auto d-flex align-items-center gap-3"><span class="text-white-50 small d-none d-sm-inline"><?= h($_SESSION['user_name']) ?></span><form action="logout.php" method="post" class="m-0"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><button type="submit" class="btn btn-sm btn-outline-light">Sair</button></form></div><?php endif; ?></div></nav>
<?php if (isset($_SESSION['flash'])): [$message, $type] = $_SESSION['flash']; unset($_SESSION['flash']); ?><div class="container pt-3"><div class="alert alert-<?= h($type) ?> mb-0" role="status"><?= h($message) ?></div></div><?php endif; ?>
