<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['user_id'])) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Informe um e-mail válido.';
    else {
        $stmt = db()->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['senha_hash'])) $error = 'E-mail ou senha incorretos.';
        else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['vault_key'] = vaultKey($password, $user['sal_criptografia']);
            redirect('index.php');
        }
    }
}
$title = 'Entrar'; require __DIR__ . '/partials/header.php';
?>
<main class="container auth-container"><div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">
<div class="brand-icon mb-3">✦</div><h1 class="h3 fw-bold">Acesse seu cofre</h1><p class="text-secondary">Entre com sua senha para abrir suas credenciais.</p>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= h($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>">
<div class="mb-3"><label for="email" class="form-label">E-mail</label><input class="form-control" type="email" id="email" name="email" value="<?= h($email ?? '') ?>" required autocomplete="username"></div>
<div class="mb-4"><label for="password" class="form-label">Senha</label><input class="form-control" type="password" id="password" name="password" required autocomplete="current-password"></div>
<button class="btn btn-primary w-100" type="submit">Entrar no cofre</button></form>
<p class="text-center text-secondary mt-4 mb-0">Primeira vez? <a href="register.php">Criar conta</a></p>
</div></div></main><?php require __DIR__ . '/partials/footer.php'; ?>
