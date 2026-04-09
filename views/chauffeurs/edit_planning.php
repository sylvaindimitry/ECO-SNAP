<?php 
$pageTitle = 'Mon Planning - ECO-SNAP';
ob_start(); 

require_once __DIR__ . '/../../core/helpers.php';

$jours = getJoursSemaine();
$planningJours = array_column($planning, 'jour_semaine');
?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-week"></i> Modifier Mon Planning
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Information :</strong> Votre planning détermine les jours où vous recevrez des notifications de signalements.
                    </div>
                    
                    <form action="<?= url('/chauffeur/save-planning') ?>" method="post">
                        <div class="row">
                            <?php foreach ($jours as $jour): ?>
                                <?php $isSelected = in_array($jour, $planningJours); ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card <?= $isSelected ? 'border-success' : '' ?>">
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="jours[]" value="<?= $jour ?>" 
                                                       id="jour_<?= $jour ?>"
                                                       <?= $isSelected ? 'checked' : '' ?>
                                                       onchange="toggleHeures('<?= $jour ?>', this.checked)">
                                                <label class="form-check-label fw-bold" for="jour_<?= $jour ?>">
                                                    <?= ucfirst($jour) ?>
                                                </label>
                                            </div>
                                            
                                            <div id="heures_<?= $jour ?>" style="display: <?= $isSelected ? 'block' : 'none' ?>">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="form-label small">Heure début</label>
                                                        <input type="time" class="form-control form-control-sm" 
                                                               name="heure_debut_<?= $jour ?>" 
                                                               value="<?= $isSelected ? '08:00' : '08:00' ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Heure fin</label>
                                                        <input type="time" class="form-control form-control-sm" 
                                                               name="heure_fin_<?= $jour ?>" 
                                                               value="<?= $isSelected ? '17:00' : '17:00' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= url('/chauffeur/dashboard') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleHeures(jour, checked) {
    const heuresDiv = document.getElementById('heures_' + jour);
    const card = heuresDiv.closest('.card');
    
    if (checked) {
        heuresDiv.style.display = 'block';
        card.classList.add('border-success');
    } else {
        heuresDiv.style.display = 'none';
        card.classList.remove('border-success');
    }
}
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
