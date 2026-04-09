<?php 
$pageTitle = 'Connexion - ECO-SNAP';
ob_start(); 
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success">ECO-SNAP</h2>
                        <p class="text-muted">Bienvenue. Veuillez vous connecter à votre compte.</p>
                    </div>
                    
                    <form action="<?= url('/login') ?>" method="post">
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control" name="email" 
                                       placeholder="votre@email.com" required 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" id="password" class="form-control" 
                                       name="password" placeholder="Votre mot de passe" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Remember -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Se rappeler de moi
                            </label>
                        </div>
                        
                        <!-- Submit -->
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                        
                        <p class="text-center mb-0">
                            Vous n'avez pas de compte ? 
                            <a href="<?= url('/register') ?>" class="text-success fw-bold">Inscrivez-vous</a>
                        </p>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">ECO-SNAP © <?= date('Y') ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
