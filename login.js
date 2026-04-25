function handleLogin() {
      const u = document.getElementById('username').value.trim();
      const p = document.getElementById('password').value.trim();
      if (!u || !p) {
        alert('Please enter both username and password.');
        return;
      }
      const msg = document.getElementById('successMsg');
      msg.style.display = 'block';
      msg.textContent = 'Welcome, ' + u + '! Logging you in...';
      setTimeout(() => {
        window.location.href = 'index.html';
      }, 1800);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') handleLogin();
    });