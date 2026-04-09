<?php 
$pageTitle = 'Signalements Disponibles - ECO-SNAP';
ob_start(); 

require_once __DIR__ . '/../../core/helpers.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <i class="bi bi-list-check"></i> Signalements Disponibles
                </h2>
                <a href="<?= url('/chauffeur/dashboard') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Zone :</strong> <?= htmlspecialchars($chauffeur['zone_nom']) ?> - <?= htmlspecialchars($chauffeur['zone_ville']) ?>
                <br>
                <small>Ces signalements sont dans votre zone et sont en attente de prise en charge.</small>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($signalements)): ?>
            <div class="col-12">
                <div class="card shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Aucun signalement disponible</h5>
                        <p class="text-muted">Tous les signalements de votre zone ont été pris en charge.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($signalements as $signalement): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <?php if (!empty($signalement['photo'])): ?>
                            <img src="<?= url($signalement['photo']) ?>" class="card-img-top" alt="Photo" 
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-geo-alt text-danger"></i> 
                                    <?= htmlspecialchars($signalement['quartier']) ?>
                                </h5>
                                <?= statutBadge($signalement['statut']) ?>
                            </div>
                            
                            <p class="text-muted mb-2">
                                <small><?= htmlspecialchars($signalement['ville']) ?></small>
                            </p>
                            
                            <div class="mb-3">
                                <span class="badge bg-<?= $signalement['type_depot'] === 'eau' ? 'info' : 'warning' ?>">
                                    <?= ucfirst($signalement['type_depot']) ?>
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-clock"></i> <?= formatDate($signalement['created_at'], 'd/m H:i') ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($signalement['description'])): ?>
                                <p class="card-text small text-muted">
                                    <?= truncate(htmlspecialchars($signalement['description']), 100) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="d-grid">
                                <a href="<?= url('/signalement/' . $signalement['id']) ?>" class="btn btn-primary mb-2">
                                    <i class="bi bi-eye"></i> Voir les détails
                                </a>
                                
                                <form action="<?= url('/chauffeur/signalement/' . $signalement['id'] . '/prendre-en-charge') ?>" 
                                      method="post">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle"></i> Prendre en charge
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
