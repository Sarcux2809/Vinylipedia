// Modal y formularios
const signInBtn = document.getElementById('signInBtn');
const registerBtn = document.getElementById('registerBtn');
const authModal = document.getElementById('authModal');
const closeModal = document.getElementById('closeModal');
const modalBackdrop = document.getElementById('modalBackdrop');

const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const forgotPasswordForm = document.getElementById('forgotPasswordForm');

const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
const backToLoginBtn = document.getElementById('backToLoginBtn');

signInBtn.addEventListener('click', () => {
  authModal.classList.add('active');
  showLoginForm();
  document.body.style.overflow = 'hidden';
});

registerBtn.addEventListener('click', () => {
  authModal.classList.add('active');
  showRegisterForm();
  document.body.style.overflow = 'hidden';
});

closeModal.addEventListener('click', closeAuthModal);
modalBackdrop.addEventListener('click', closeAuthModal);

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && authModal.classList.contains('active')) {
    closeAuthModal();
  }
});

loginTab.addEventListener('click', showLoginForm);
registerTab.addEventListener('click', showRegisterForm);
forgotPasswordBtn.addEventListener('click', showForgotPasswordForm);
backToLoginBtn.addEventListener('click', showLoginForm);

function closeAuthModal() {
  authModal.classList.remove('active');
  document.body.style.overflow = 'auto';
}

function showLoginForm() {
  loginForm.classList.remove('hidden');
  registerForm.classList.add('hidden');
  forgotPasswordForm.classList.add('hidden');
  loginTab.classList.add('active');
  registerTab.classList.remove('active');
}

function showRegisterForm() {
  loginForm.classList.add('hidden');
  registerForm.classList.remove('hidden');
  forgotPasswordForm.classList.add('hidden');
  loginTab.classList.remove('active');
  registerTab.classList.add('active');
}

function showForgotPasswordForm() {
  loginForm.classList.add('hidden');
  registerForm.classList.add('hidden');
  forgotPasswordForm.classList.remove('hidden');
  loginTab.classList.remove('active');
  registerTab.classList.remove('active');
}

// LOGIN
loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'login', email, password })
  });

  const data = await res.json();
  if (data.success) {
    alert('Inicio de sesión exitoso');
    closeAuthModal();
    location.reload(); // para reflejar el estado de sesión
  } else {
    alert(data.error || 'Error al iniciar sesión');
  }
});

// REGISTRO
registerForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('registerUsername').value;
  const email = document.getElementById('registerEmail').value;
  const password = document.getElementById('registerPassword').value;

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'register', username, email, password })
  });

  const data = await res.json();
  if (data.success) {
    alert('Registro exitoso');
    closeAuthModal();
  } else {
    alert(data.error || 'Error al registrarse');
  }
});

// RECUPERAR CONTRASEÑA
forgotPasswordForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('forgotEmail').value;

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'recover', email })
  });

  const data = await res.json();
  alert(data.message || 'Se ha enviado un enlace de recuperación');
  showLoginForm();
});

// VERIFICAR SESIÓN
async function checkSession() {
  const res = await fetch('session.php');
  const data = await res.json();

  if (data.loggedIn) {
    const header = document.querySelector('header .md\\:flex.items-center');
    header.innerHTML = `
      <span class="text-white text-sm mr-4">Hola, ${data.user.username}</span>
      <button id="logoutBtn"
        class="bg-red-500 text-white rounded-full px-4 py-1 text-sm font-medium hover:bg-red-600 transition">
        Cerrar sesión
      </button>
    `;
    document.getElementById('logoutBtn').addEventListener('click', async () => {
      await fetch('logout.php');
      location.reload();
    });
  }
}

checkSession();
