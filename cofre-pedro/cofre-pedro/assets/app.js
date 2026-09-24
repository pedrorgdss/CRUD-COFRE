document.querySelectorAll('.delete-form').forEach(form => form.addEventListener('submit', event => {
  if (!confirm('Excluir esta credencial definitivamente?')) event.preventDefault();
}));

document.querySelectorAll('.toggle-password').forEach(button => button.addEventListener('click', () => {
  const field = document.getElementById(button.dataset.target);
  field.type = field.type === 'password' ? 'text' : 'password';
  button.textContent = field.type === 'password' ? 'Mostrar' : 'Ocultar';
}));

document.querySelectorAll('.generate-password').forEach(button => button.addEventListener('click', () => {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*';
  const random = new Uint32Array(20);
  crypto.getRandomValues(random);
  document.getElementById(button.dataset.target).value = Array.from(random, n => chars[n % chars.length]).join('');
}));

document.querySelectorAll('.reveal-btn').forEach(button => button.addEventListener('click', async () => {
  const box = button.closest('.card-body').querySelector('.secret-box');
  const value = box.querySelector('.secret-value');
  if (!box.classList.contains('d-none')) { value.textContent = ''; box.classList.add('d-none'); button.textContent = 'Mostrar senha'; return; }
  button.disabled = true;
  try {
    const body = new URLSearchParams({id: button.dataset.id, csrf: window.vaultToken});
    const response = await fetch('reveal.php', {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body, cache: 'no-store'});
    const data = await response.json();
    if (!response.ok) throw new Error(data.erro || 'Erro ao abrir a senha.');
    value.textContent = data.senha;
    box.classList.remove('d-none'); button.textContent = 'Ocultar senha';
  } catch (error) { alert(error.message); } finally { button.disabled = false; }
}));

document.querySelectorAll('.copy-btn').forEach(button => button.addEventListener('click', async () => {
  try {
    await navigator.clipboard.writeText(button.parentElement.querySelector('.secret-value').textContent);
    button.textContent = 'Copiado!'; setTimeout(() => { button.textContent = 'Copiar'; }, 1800);
  } catch { alert('Não foi possível copiar. Use HTTPS ou localhost.'); }
}));
