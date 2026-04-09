<?php 
$pageTitle = 'Détails du Signalement - ECO-SNAP';
ob_start(); 

require_once __DIR__ . '/../../core/helpers.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-exclamation-triangle text-warning"></i> 
                            Signalement #<?= $signalement['id'] ?>
                        </h4>
                        <?= statutBadge($signalement['statut']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-8">
                            <h5 class="mb-3">Informations</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><i class="bi bi-geo-alt"></i> Ville :</strong>
                                    <p><?= htmlspecialchars($signalement['ville']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="bi bi-pin-map"></i> Quartier :</strong>
                                    <p><?= htmlspecialchars($signalement['quartier']) ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><i class="bi bi-tag"></i> Type :</strong>
                                    <p>
                                        <span class="badge bg-<?= $signalement['type_depot'] === 'eau' ? 'info' : 'warning' ?>">
                                            <?= ucfirst($signalement['type_depot']) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="bi bi-map"></i> Zone :</strong>
                                    <p><?= htmlspecialchars($signalement['zone_nom']) ?> - <?= htmlspecialchars($signalement['zone_ville']) ?></p>
                                </div>
                            </div>
                            
                            <?php if (!empty($signalement['description'])): ?>
                                <div class="mb-3">
                                    <strong><i class="bi bi-card-text"></i> Description :</strong>
                                    <p class="mt-2"><?= nl2br(htmlspecialchars($signalement['description'])) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($signalement['photo'])): ?>
                                <div class="mb-3">
                                    <strong><i class="bi bi-camera"></i> Photo :</strong>
                                    <div class="mt-2">
                                        <img src="<?= url($signalement['photo']) ?>" alt="Photo du signalement" 
                                             class="img-fluid rounded" style="max-height: 400px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($signalement['latitude'] && $signalement['longitude']): ?>
                                <div class="mb-3">
                                    <strong><i class="bi bi-geo"></i> Coordonnées GPS :</strong>
                                    <p><?= $signalement['latitude'] ?>, <?= $signalement['longitude'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Métadonnées -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Détails</h6>
                                    
                                    <p class="mb-2">
                                        <strong>Signalé par :</strong><br>
                                        <?= htmlspecialchars($signalement['user_nom']) ?> 
                                        <?= htmlspecialchars($signalement['user_prenom']) ?>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($signalement['user_email']) ?></small>
                                    </p>
                                    
                                    <hr>
                                    
                                    <p class="mb-2">
                                        <strong>Date du signalement :</strong><br>
                                        <?= formatDate($signalement['created_at']) ?>
                                    </p>
                                    
                                    <?php if ($signalement['date_prise_en_charge']): ?>
                                        <p class="mb-2">
                                            <strong>Pris en charge le :</strong><br>
                                            <?= formatDate($signalement['date_prise_en_charge']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($signalement['date_resolution']): ?>
                                        <p class="mb-2">
                                            <strong>Résolu le :</strong><br>
                                            <?= formatDate($signalement['date_resolution']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($signalement['chauffeur_nom'])): ?>
                                        <hr>
                                        <p class="mb-2">
                                            <strong><i class="bi bi-truck"></i> Assigné à :</strong><br>
                                            <?= htmlspecialchars($signalement['chauffeur_nom']) ?> 
                                            <?= htmlspecialchars($signalement['chauffeur_prenom']) ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($signalement['nom_equipe'] ?? '') ?>
                                            </small>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="mt-4 pt-3 border-top">
                        <a href="<?= url('/dashboard') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour au dashboard
                        </a>
                        
                        <?php if (hasRole('chauffeur') && $signalement['statut'] === 'en_attente'): ?>
                            <form action="<?= url('/chauffeur/signalement/' . $signalement['id'] . '/prendre-en-charge') ?>" 
                                  method="post" class="d-inline">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Prendre en charge
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
