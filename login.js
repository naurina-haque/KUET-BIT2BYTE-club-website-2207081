function handleLogin() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const remember = document.getElementById('remember').checked;
    
    if (!email || !password) {
        alert('Please enter both email and password.');
        return;
    }
    
    const msg = document.getElementById('successMsg');
    msg.style.display = 'block';
    msg.textContent = 'Logging in...';
    msg.style.color = '#666';
    
    // Send login request to PHP backend
    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email, password: password, remember: remember })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            msg.textContent = data.message;
            msg.style.color = '#28a745';
setTimeout(() => {
                 window.location.href = data.redirect || 'admin.php';
             }, 1500);
        } else {
            msg.textContent = data.message;
            msg.style.color = '#dc3545';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        msg.textContent = 'An error occurred. Please try again.';
        msg.style.color = '#dc3545';
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') handleLogin();
});