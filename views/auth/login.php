<?php 
$pageTitle = 'Connexion - ECO-SNAP';
ob_start(); 
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success">🌍 ECO-SNAP</h2>
                        <p class="text-muted">Bienvenue. Veuillez vous connecter à votre compte.</p>
                    </div>
                    
                    <form action="<?= url('/login') ?>" method="post">
                        <!-- CSRF Token -->
                        <?= \CSRF::field() ?>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control" name="email" 
                                       placeholder="votre@email.com" required autocomplete="email"
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
                                       name="password" placeholder="Votre mot de passe" required autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Remember me -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Se souvenir de moi
                            </label>
                        </div>
                        
                        <!-- Submit -->
                        <button type="submit" class="btn btn-success w-100 mb-3" id="loginBtn">
                            <span class="btn-text"><i class="bi bi-box-arrow-in-right"></i> Se connecter</span>
                            <span class="spinner-border spinner-border-sm d-none" id="loginSpinner"></span>
                        </button>
                        
                        <!-- Divider -->
                        <div class="text-center my-3">
                            <hr>
                            <small class="text-muted px-3 bg-white">OU</small>
                        </div>
                        
                        <!-- Google Login -->
                        <a href="<?= url('/auth/google') ?>" class="btn btn-outline-danger w-100 mb-3">
                            <i class="bi bi-google"></i> Continuer avec Google
                        </a>
                        
                        <p class="text-center mb-2">
                            Vous n'avez pas de compte ? 
                            <a href="<?= url('/register') ?>" class="text-success fw-bold">Inscrivez-vous</a>
                        </p>
                        <p class="text-center mb-0">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#resendVerificationModal" class="text-muted small">
                                Renvoyer l'email de vérification
                            </a>
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

<!-- Modal: Renvoyer l'email de vérification -->
<div class="modal fade" id="resendVerificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-envelope-check"></i> Renvoyer l'email de vérification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/auth/resend-verification') ?>" method="post">
                <?= \CSRF::field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" class="form-control" name="email" required placeholder="votre@email.com">
                    </div>
                    <p class="text-muted small mb-0">
                        Nous vous enverrons un nouvel email de vérification à cette adresse.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Renvoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
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
    
    // Show loading spinner on form submit
    const loginForm = document.querySelector('form');
    const loginBtn = document.querySelector('#loginBtn');
    const btnText = document.querySelector('.btn-text');
    const spinner = document.querySelector('#loginSpinner');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
            loginBtn.disabled = true;
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
