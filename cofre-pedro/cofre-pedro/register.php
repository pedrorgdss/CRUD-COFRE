<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['user_id'])) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkToken();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($name === '' || mb_strlen($name) > 120) $error = 'Informe um nome de até 120 caracteres.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $error = 'Informe um e-mail válido.';
    elseif (strlen($password) < 12) $error = 'Use pelo menos 12 caracteres na senha.';
    elseif (!hash_equals($password, (string) ($_POST['confirm'] ?? ''))) $error = 'As senhas não conferem.';
    else {
        try {
            $stmt = db()->prepare('INSERT INTO usuarios (nome, email, senha_hash, sal_criptografia) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), random_bytes(16)]);
            flash('Conta criada. Entre com sua senha.');
            redirect('login.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $error = 'Este e-mail já está cadastrado.';
            else throw $e;
        }
    }
}
$title = 'Criar conta'; require __DIR__ . '/partials/header.php';
?>
<main class="container auth-container"><div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">
<div class="brand-icon mb-3">✦</div><h1 class="h3 fw-bold">Crie seu cofre</h1><p class="text-secondary">Escolha uma senha forte e guarde-a em lugar seguro.</p>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= h($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>">
<div class="mb-3"><label for="name" class="form-label">Nome</label><input class="form-control" id="name" name="name" maxlength="120" value="<?= h($name ?? '') ?>" required></div>
<div class="mb-3"><label for="email" class="form-label">E-mail</label><input class="form-control" type="email" id="email" name="email" maxlength="190" value="<?= h($email ?? '') ?>" required autocomplete="username"></div>
<div class="mb-3"><label for="password" class="form-label">Senha</label><input class="form-control" type="password" id="password" name="password" minlength="12" required autocomplete="new-password"><div class="form-text">Mínimo de 12 caracteres. Se esquecê-la, as senhas guardadas não poderão ser recuperadas.</div></div>
<div class="mb-4"><label for="confirm" class="form-label">Confirmar senha</label><input class="form-control" type="password" id="confirm" name="confirm" minlength="12" required autocomplete="new-password"></div>
<button class="btn btn-primary w-100" type="submit">Criar conta</button></form>
<p class="text-center text-secondary mt-4 mb-0">Já tem conta? <a href="login.php">Entrar</a></p>
</div></div></main><?php require __DIR__ . '/partials/footer.php'; ?>
