<?php
$pageTitle = 'Dashboard - ECO-SNAP';
ob_start();
?>

<!-- Welcome Modal (affiche seulement au premier login) -->
<?php if (isset($_SESSION['show_welcome']) && $_SESSION['show_welcome']): ?>
    <?php unset($_SESSION['show_welcome']); ?>
    <div class="modal fade" id="welcomeModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title">
                        <i class="bi bi-emoji-smile"></i> Bienvenue <?= htmlspecialchars($_SESSION['user_first_name'] ?? 'Utilisateur') ?> !
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Connexion réussie !</h4>
                    <p class="text-muted mb-3">
                        Bonjour <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></strong>,<br>
                        Vous êtes maintenant connecté à votre espace ECO-SNAP.
                    </p>
                    <div class="row text-start mt-3">
                        <div class="col-12">
                            <p class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i> Signalez des dépôts d'ordures</p>
                            <p class="mb-2"><i class="bi bi-bell text-warning me-2"></i> Recevez des notifications en temps réel</p>
                            <p class="mb-0"><i class="bi bi-graph-up text-success me-2"></i> Suivez l'évolution de vos signalements</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-right-circle"></i> Commencer
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">
                <i class="bi bi-speedometer2"></i> Dashboard
            </h2>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total signalements</h6>
                            <h3 class="mb-0"><?= $stats['total'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">En attente</h6>
                            <h3 class="mb-0"><?= $stats['en_attente'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-clock" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Pris en charge</h6>
                            <h3 class="mb-0"><?= $stats['pris_en_charge'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Terminés</h6>
                            <h3 class="mb-0"><?= $stats['termines'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Mes signalements -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Mes signalements
                        <a href="<?= url('/signalement/create') ?>" class="btn btn-sm btn-success float-end">
                            <i class="bi bi-plus"></i> Nouveau
                        </a>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($mesSignalements)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Aucun signalement pour le moment</p>
                            <a href="<?= url('/signalement/create') ?>" class="btn btn-primary">
                                Faire un signalement
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Lieu</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($mesSignalements, 0, 10) as $signalement): ?>
                                        <tr>
                                            <td>#<?= $signalement['id'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($signalement['ville']) ?>, 
                                                <?= htmlspecialchars($signalement['quartier']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $signalement['type_depot'] === 'eau' ? 'info' : 'warning' ?>">
                                                    <?= ucfirst($signalement['type_depot']) ?>
                                                </span>
                                            </td>
                                            <td><?= statutBadge($signalement['statut']) ?></td>
                                            <td><?= formatDate($signalement['created_at'], 'd/m/Y') ?></td>
                                            <td>
                                                <a href="<?= url('/signalement/' . $signalement['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Notifications & Zones -->
        <div class="col-lg-4">
            <!-- Notifications -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-bell"></i> Notifications
                        <?php if (!empty($notificationsNonLues)): ?>
                            <span class="badge bg-danger"><?= count($notificationsNonLues) ?></span>
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($notificationsNonLues)): ?>
                        <p class="text-muted text-center mb-0">Aucune notification</p>
                    <?php else: ?>
                        <?php foreach (array_slice($notificationsNonLues, 0, 5) as $notif): ?>
                            <div class="mb-3 pb-2 border-bottom">
                                <small class="text-muted"><?= formatDate($notif['created_at'], 'd/m H:i') ?></small>
                                <p class="mb-0"><?= htmlspecialchars($notif['message']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Zones actives -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-map"></i> Zones actives</h6>
                </div>
                <div class="card-body">
                    <?php foreach (array_slice($zones, 0, 7) as $zone): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong><?= htmlspecialchars($zone['nom']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($zone['ville']) ?></small>
                            </div>
                            <span class="badge bg-primary"><?= $zone['signalements_count'] ?? 0 ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-open welcome modal -->
<?php if (isset($_SESSION['show_welcome']) && $_SESSION['show_welcome']): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
    welcomeModal.show();
});
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
