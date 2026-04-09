<?php 
$pageTitle = 'Signaler un dépôt - ECO-SNAP';
ob_start(); 
?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success">
                            <i class="bi bi-exclamation-triangle"></i> Signaler un dépôt d'ordure
                        </h2>
                        <p class="text-muted">Aidez-nous à garder l'environnement propre en signalant les dépôts sauvages</p>
                    </div>
                    
                    <form action="<?= url('/signalement/store') ?>" method="post" enctype="multipart/form-data">
                        <!-- Zone -->
                        <div class="mb-3">
                            <label class="form-label">Zone *</label>
                            <select class="form-select" name="zone_id" id="zoneSelect" required>
                                <option value="">Sélectionnez la zone</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?= $zone['id'] ?>" 
                                            data-ville="<?= htmlspecialchars($zone['ville']) ?>"
                                            <?= (($_POST['zone_id'] ?? '') == $zone['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($zone['nom']) ?> - <?= htmlspecialchars($zone['ville']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Ville -->
                        <div class="mb-3">
                            <label class="form-label">Ville *</label>
                            <input type="text" class="form-control" name="ville" id="villeInput" 
                                   placeholder="Ex: Douala" required 
                                   value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                        </div>
                        
                        <!-- Quartier -->
                        <div class="mb-3">
                            <label class="form-label">Quartier *</label>
                            <input type="text" class="form-control" name="quartier" 
                                   placeholder="Ex: Bonamoussadi" required 
                                   value="<?= htmlspecialchars($_POST['quartier'] ?? '') ?>">
                        </div>
                        
                        <!-- Type de dépôt -->
                        <div class="mb-3">
                            <label class="form-label">Type de dépôt *</label>
                            <select class="form-select" name="type_depot" required>
                                <option value="">Choisir...</option>
                                <option value="terre" <?= (($_POST['type_depot'] ?? '') === 'terre') ? 'selected' : '' ?>>
                                    Sur terre
                                </option>
                                <option value="eau" <?= (($_POST['type_depot'] ?? '') === 'eau') ? 'selected' : '' ?>>
                                    Dans l'eau
                                </option>
                                <option value="mixte" <?= (($_POST['type_depot'] ?? '') === 'mixte') ? 'selected' : '' ?>>
                                    Mixte
                                </option>
                            </select>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Décrivez le dépôt d'ordures..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Photo -->
                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control" name="photo" accept="image/*" capture="environment">
                            <small class="text-muted">Prenez une photo du dépôt (JPEG, PNG, max 5MB)</small>
                        </div>
                        
                        <!-- Géolocalisation -->
                        <div class="mb-3">
                            <label class="form-label">Géolocalisation</label>
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button" id="geolocateBtn">
                                    <i class="bi bi-geo-alt"></i> Ma position
                                </button>
                                <input type="text" class="form-control" id="latitude" name="latitude" 
                                       placeholder="Latitude" readonly>
                                <input type="text" class="form-control" id="longitude" name="longitude" 
                                       placeholder="Longitude" readonly>
                            </div>
                        </div>
                        
                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= url('/dashboard') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Signaler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-remplir la ville quand on sélectionne une zone
    const zoneSelect = document.getElementById('zoneSelect');
    const villeInput = document.getElementById('villeInput');
    
    if (zoneSelect && villeInput) {
        zoneSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.ville) {
                villeInput.value = selectedOption.dataset.ville;
            }
        });
    }
    
    // Géolocalisation
    const geolocateBtn = document.getElementById('geolocateBtn');
    if (geolocateBtn) {
        geolocateBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    alert('Erreur de géolocalisation: ' + error.message);
                });
            } else {
                alert('La géolocalisation n\'est pas supportée par votre navigateur');
            }
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
