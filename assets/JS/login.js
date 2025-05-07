function loginUser() {
  const email = document.getElementById('email').value;
  const password = document.getElementById('pass').value;
  const loginError = document.getElementById('login-error');

  fetch('login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email: email, password: password })
  })
    .then(response => response.json())
    .then(data => {
      if (data.jwt) {
        localStorage.setItem('jwt', data.jwt);
        window.location.href = 'index.php'; // Redirect to the protected page
      } else {
        loginError.textContent = data.message;
      }
    })
    .catch(error => console.error('Error:', error));
}
