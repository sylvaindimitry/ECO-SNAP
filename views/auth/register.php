<?php 
$pageTitle = 'Inscription - ECO-SNAP';
ob_start(); 

$zoneModel = new ZoneModel();
$zones = $zoneModel->findAll();
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success">🌍 ECO-SNAP</h2>
                        <p class="text-muted">Créez votre compte pour participer à la protection de l'environnement</p>
                    </div>
                    
                    <!-- Google Register Button -->
                    <a href="<?= url('/auth/google') ?>" class="btn btn-outline-danger w-100 mb-3">
                        <i class="bi bi-google"></i> S'inscrire avec Google
                    </a>
                    
                    <!-- Divider -->
                    <div class="text-center my-3">
                        <hr>
                        <small class="text-muted px-3 bg-white">OU</small>
                    </div>
                    
                    <form action="<?= url('/register') ?>" method="post" id="registerForm">
                        <!-- CSRF Token -->
                        <?= \CSRF::field() ?>
                        
                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="nom" required 
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                            
                            <!-- Prénom -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom *</label>
                                <input type="text" class="form-control" name="prenom" required 
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <!-- Téléphone -->
                        <div class="mb-3">
                            <label class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" name="telephone" required 
                                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                        
                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label class="form-label">Mot de passe *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" required minlength="6" id="registerPassword">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 6 caractères</small>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" class="form-control" name="password_confirmation" required minlength="6">
                        </div>
                        
                        <!-- Type de compte -->
                        <div class="mb-3">
                            <label class="form-label">Type de compte *</label>
                            <select class="form-select" name="role" id="roleSelect" required>
                                <option value="habitant" <?= (($_POST['role'] ?? '') === 'habitant') ? 'selected' : '' ?>>
                                    👤 Habitant - Signaler des dépôts
                                </option>
                                <option value="chauffeur" <?= (($_POST['role'] ?? '') === 'chauffeur') ? 'selected' : '' ?>>
                                    🚛 Chauffeur/Équipe - Collecter les dépôts
                                </option>
                            </select>
                        </div>
                        
                        <!-- Champs supplémentaires pour chauffeur -->
                        <div id="chauffeurFields" style="display: none;">
                            <hr>
                            <h5 class="mb-3">📋 Informations chauffeur/équipe</h5>
                            
                            <!-- Zone -->
                            <div class="mb-3">
                                <label class="form-label">Zone d'intervention *</label>
                                <select class="form-select" name="zone_id">
                                    <option value="">Sélectionnez votre zone</option>
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?= $zone['id'] ?>" 
                                                <?= (($_POST['zone_id'] ?? '') == $zone['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($zone['nom']) ?> - <?= htmlspecialchars($zone['ville']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Nom de l'équipe -->
                            <div class="mb-3">
                                <label class="form-label">Nom de l'équipe</label>
                                <input type="text" class="form-control" name="nom_equipe" 
                                       placeholder="Ex: Team Alpha"
                                       value="<?= htmlspecialchars($_POST['nom_equipe'] ?? '') ?>">
                            </div>
                            
                            <!-- Type de véhicule -->
                            <div class="mb-3">
                                <label class="form-label">Type de véhicule</label>
                                <select class="form-select" name="vehicule_type">
                                    <option value="">Sélectionnez</option>
                                    <option value="Camion" <?= (($_POST['vehicule_type'] ?? '') === 'Camion') ? 'selected' : '' ?>>Camion</option>
                                    <option value="Camionnette" <?= (($_POST['vehicule_type'] ?? '') === 'Camionnette') ? 'selected' : '' ?>>Camionnette</option>
                                    <option value="Tricycle" <?= (($_POST['vehicule_type'] ?? '') === 'Tricycle') ? 'selected' : '' ?>>Tricycle</option>
                                    <option value="Autre" <?= (($_POST['vehicule_type'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                            
                            <!-- Immatriculation -->
                            <div class="mb-3">
                                <label class="form-label">Immatriculation</label>
                                <input type="text" class="form-control" name="immatriculation" 
                                       placeholder="Ex: AA-1234-BB"
                                       value="<?= htmlspecialchars($_POST['immatriculation'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                            <label class="form-check-label small" for="acceptTerms">
                                J'accepte les <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">conditions d'utilisation</a>
                            </label>
                        </div>
                        
                        <!-- Submit -->
                        <button type="submit" class="btn btn-success w-100 mb-3" id="registerBtn">
                            <span class="btn-text"><i class="bi bi-person-plus"></i> S'inscrire</span>
                            <span class="spinner-border spinner-border-sm d-none" id="registerSpinner"></span>
                        </button>
                        
                        <p class="text-center mb-0">
                            Déjà un compte ? 
                            <a href="<?= url('/login') ?>" class="text-success fw-bold">Connectez-vous</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Conditions d'utilisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>En utilisant ECO-SNAP, vous acceptez de :</p>
                <ul>
                    <li>Fournir des informations exactes</li>
                    <li>Ne pas signaler de faux dépôts d'ordures</li>
                    <li>Respecter les autres utilisateurs</li>
                    <li>Protéger vos identifiants de connexion</li>
                </ul>
                <p class="text-muted small">ECO-SNAP se réserve le droit de suspendre tout compte abusif.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">J'ai compris</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle chauffeur fields
    const roleSelect = document.getElementById('roleSelect');
    const chauffeurFields = document.getElementById('chauffeurFields');
    
    function toggleChauffeurFields() {
        if (roleSelect.value === 'chauffeur') {
            chauffeurFields.style.display = 'block';
        } else {
            chauffeurFields.style.display = 'none';
        }
    }
    
    roleSelect.addEventListener('change', toggleChauffeurFields);
    toggleChauffeurFields();
    
    // Toggle password visibility
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#registerPassword');
    
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
    const registerForm = document.querySelector('#registerForm');
    const registerBtn = document.querySelector('#registerBtn');
    const btnText = document.querySelector('#registerBtn .btn-text');
    const spinner = document.querySelector('#registerSpinner');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
            registerBtn.disabled = true;
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
