<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #0f172a;
            --accent-color: #60a5fa;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --shadow-color: rgba(30, 58, 138, 0.3);
            --error-color: #ef4444;
            --success-color: #10b981;
            --input-bg: #0f172a;
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--text-primary);
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }
        
        .login-card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.5);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: float 20s linear infinite;
            opacity: 0.3;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .brand-logo i {
            font-size: 2.5rem;
            color: white;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        
        .brand-text {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
            background: linear-gradient(to right, #ffffff, #dbeafe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .brand-subtitle {
            font-size: 0.95rem;
            opacity: 0.8;
            font-weight: 400;
            letter-spacing: 0.5px;
            color: #dbeafe;
        }
        
        .card-body {
            padding: 2.5rem 2rem;
            background: var(--card-bg);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }
        
        .input-field {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--input-bg);
            color: var(--text-primary);
            outline: none;
        }
        
        .input-field:focus {
            border-color: var(--primary-light);
            background: #1a2536;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }
        
        .input-field.has-error {
            border-color: var(--error-color);
            animation: shake 0.5s ease;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .input-field:focus + .input-icon {
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }
        
        .floating-label {
            position: absolute;
            left: 3rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: all 0.3s ease;
            pointer-events: none;
            background: var(--input-bg);
            padding: 0 0.5rem;
            font-size: 0.95rem;
        }
        
        .input-field:focus ~ .floating-label,
        .input-field:not(:placeholder-shown) ~ .floating-label {
            top: 0;
            font-size: 0.8rem;
            color: var(--accent-color);
            background: #1a2536;
            transform: translateY(-50%);
        }
        
        .input-field::placeholder {
            color: transparent;
        }
        
        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideDown 0.3s ease;
        }
        
        .error-message i {
            font-size: 0.9rem;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            padding-left: 2rem;
        }
        
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .checkmark {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 20px;
            background-color: var(--input-bg);
            border: 2px solid var(--border-color);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .checkbox-container:hover input ~ .checkmark {
            border-color: var(--primary-light);
        }
        
        .checkbox-container input:checked ~ .checkmark {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        .checkmark::after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .checkbox-container input:checked ~ .checkmark::after {
            display: block;
        }
        
        .forgot-link {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }
        
        .login-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.6s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
        }
        
        .login-btn:hover::before {
            left: 100%;
        }
        
        .login-btn:active {
            transform: translateY(-1px);
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        .login-btn.loading .btn-text {
            display: none;
        }
        
        .login-btn.loading .loading-spinner {
            display: block;
        }
        
        .success-message {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideUp 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            100% {
                transform: translate(-15px, -15px) rotate(360deg);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.5); }
        }
        
        .glow-effect {
            animation: glow 2s ease-in-out infinite;
        }
        
        @media (max-width: 576px) {
            .login-container {
                max-width: 100%;
            }
            
            .card-header {
                padding: 2rem 1.5rem;
            }
            
            .card-body {
                padding: 2rem 1.5rem;
            }
            
            .brand-text {
                font-size: 1.8rem;
            }
            
            .brand-logo {
                width: 70px;
                height: 70px;
            }
            
            .brand-logo i {
                font-size: 2rem;
            }
        }
        
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        
        .footer-text a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer-text a:hover {
            text-decoration: underline;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--input-bg);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }
        
        /* Selection color */
        ::selection {
            background-color: rgba(59, 130, 246, 0.3);
            color: white;
        }
        
        /* Focus outline for accessibility */
        *:focus {
            outline: 2px solid var(--accent-color);
            outline-offset: 2px;
        }
        
        *:focus:not(.input-field) {
            outline: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card glow-effect">
            <div class="card-header">
                <div class="brand-logo">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1 class="brand-text">INVENTORY SYSTEM</h1>
                <p class="brand-subtitle">Secure Admin Portal</p>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                    @csrf
                    
                    <div class="input-group">
                        <input type="text" 
                               class="input-field @error('username') has-error @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}"
                               placeholder=" "
                               autocomplete="username"
                               required>
                        <i class="bi bi-person input-icon"></i>
                        <label for="username" class="floating-label">Username</label>
                        
                        @error('username')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="input-group">
                        <input type="password" 
                               class="input-field @error('password') has-error @enderror" 
                               id="password" 
                               name="password"
                               placeholder=" "
                               autocomplete="current-password"
                               required>
                        <i class="bi bi-key input-icon"></i>
                        <label for="password" class="floating-label">Password</label>
                        
                        @error('password')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="remember-forgot">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">Remember me</span>
                        </label>
                        
                        <!-- <a href="#" class="forgot-link">Forgot password?</a> -->
                    </div>
                    
                    <button type="submit" class="login-btn" id="loginButton">
                        <span class="btn-text"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</span>
                        <div class="loading-spinner"></div>
                    </button>
                    
                    @if(session('status'))
                        <div class="success-message">
                            <i class="bi bi-check-circle"></i>
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="error-message" style="margin-top: 1rem;">
                            <i class="bi bi-x-circle"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="success-message">
                            <i class="bi bi-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                </form>
                
                <!-- <div class="footer-text">
                    <p>Need assistance? <a href="#">Contact System Administrator</a></p>
                    <p class="mt-2 text-xs opacity-50"><i class="bi bi-shield-check"></i> Secured by Laravel Authentication</p>
                </div> -->
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const inputs = document.querySelectorAll('.input-field');
            
            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
                
                // Auto-validate on input
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('has-error');
                    }
                });
            });
            
            // Form submission with loading state
            loginForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous errors
                document.querySelectorAll('.has-error').forEach(el => {
                    el.classList.remove('has-error');
                });
                
                // Validate username
                const username = document.getElementById('username');
                if (!username.value.trim()) {
                    username.classList.add('has-error');
                    isValid = false;
                }
                
                // Validate password
                const password = document.getElementById('password');
                if (!password.value.trim()) {
                    password.classList.add('has-error');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Shake animation for error fields
                    document.querySelectorAll('.has-error').forEach(el => {
                        el.style.animation = 'none';
                        el.offsetHeight; // Trigger reflow
                        el.style.animation = 'shake 0.5s ease';
                    });
                } else {
                    // Show loading state
                    loginButton.classList.add('loading');
                    loginButton.disabled = true;
                    
                    // Add a subtle effect to the card
                    document.querySelector('.login-card').style.transform = 'scale(0.99)';
                }
            });
            
            // Auto-focus username field with slight delay for animation
            setTimeout(() => {
                document.getElementById('username').focus();
            }, 300);
            
            // Show/hide password functionality
            const passwordField = document.getElementById('password');
            
            // Create toggle password button
            const togglePassword = document.createElement('span');
            togglePassword.innerHTML = '<i class="bi bi-eye"></i>';
            togglePassword.className = 'password-toggle';
            togglePassword.style.position = 'absolute';
            togglePassword.style.right = '1rem';
            togglePassword.style.top = '50%';
            togglePassword.style.transform = 'translateY(-50%)';
            togglePassword.style.cursor = 'pointer';
            togglePassword.style.color = 'var(--text-secondary)';
            togglePassword.style.transition = 'color 0.3s ease';
            togglePassword.style.zIndex = '2';
            togglePassword.style.fontSize = '1.1rem';
            
            passwordField.parentElement.appendChild(togglePassword);
            passwordField.parentElement.style.position = 'relative';
            
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                
                // Add animation
                this.style.transform = 'translateY(-50%) scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-50%) scale(1)';
                }, 200);
            });
            
            togglePassword.addEventListener('mouseenter', function() {
                this.style.color = 'var(--accent-color)';
            });
            
            togglePassword.addEventListener('mouseleave', function() {
                if (passwordField !== document.activeElement) {
                    this.style.color = 'var(--text-secondary)';
                }
            });
            
            // Password field focus/blur effects on toggle button
            passwordField.addEventListener('focus', function() {
                togglePassword.style.color = 'var(--accent-color)';
            });
            
            passwordField.addEventListener('blur', function() {
                togglePassword.style.color = 'var(--text-secondary)';
            });
            
            // Add floating particles effect
            const createParticle = () => {
                const particle = document.createElement('div');
                particle.style.position = 'absolute';
                particle.style.width = Math.random() * 4 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.background = 'rgba(255, 255, 255, 0.1)';
                particle.style.borderRadius = '50%';
                particle.style.top = Math.random() * 100 + 'vh';
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '-1';
                document.body.appendChild(particle);
                
                // Animate
                const duration = Math.random() * 10 + 10;
                particle.animate([
                    { transform: 'translateY(0px)', opacity: 0 },
                    { transform: `translateY(${Math.random() * -100 - 50}px)`, opacity: 0.5 },
                    { transform: `translateY(${Math.random() * -200 - 100}px)`, opacity: 0 }
                ], {
                    duration: duration * 1000,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
                });
                
                // Remove after animation
                setTimeout(() => particle.remove(), duration * 1000);
            };
            
            // Create initial particles
            for (let i = 0; i < 15; i++) {
                setTimeout(createParticle, i * 300);
            }
            
            // Continue creating particles
            setInterval(createParticle, 2000);
        });
    </script>
</body>
</html>