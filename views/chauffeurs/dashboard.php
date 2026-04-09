<?php 
$pageTitle = 'Dashboard Chauffeur - ECO-SNAP';
ob_start(); 

require_once __DIR__ . '/../../core/helpers.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-truck text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title"><?= htmlspecialchars($chauffeur['nom_equipe'] ?? 'Chauffeur') ?></h5>
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($chauffeur['nom']) ?> <?= htmlspecialchars($chauffeur['prenom']) ?>
                    </p>
                    <p class="text-muted small">
                        Zone: <?= htmlspecialchars($chauffeur['zone_nom']) ?> - <?= htmlspecialchars($chauffeur['zone_ville']) ?>
                    </p>
                    <span class="badge bg-<?= $chauffeur['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($chauffeur['statut']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/chauffeur/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/chauffeur/signalements-disponibles') ?>">
                                <i class="bi bi-list-check"></i> Signalements disponibles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/chauffeur/edit-planning') ?>">
                                <i class="bi bi-calendar-week"></i> Mon planning
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/chauffeur/statistiques') ?>">
                                <i class="bi bi-graph-up"></i> Statistiques
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Notifications -->
            <?php if (!empty($notificationsNonLues)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-bell"></i> Notifications non lues (<?= count($notificationsNonLues) ?>)</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach (array_slice($notificationsNonLues, 0, 3) as $notif): ?>
                            <li><?= htmlspecialchars($notif['message']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($notificationsNonLues) > 3): ?>
                        <small>... et <?= count($notificationsNonLues) - 3 ?> autre(s)</small>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Signalements assignés</h6>
                                    <h2 class="mb-0"><?= count($signalementsAssignes) ?></h2>
                                </div>
                                <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Terminés</h6>
                                    <h2 class="mb-0"><?= count($signalementsTermines) ?></h2>
                                </div>
                                <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Jours de travail cette semaine</h6>
                                    <h2 class="mb-0"><?= count($planning) ?></h2>
                                </div>
                                <i class="bi bi-calendar-week" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Planning de la semaine -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Mon planning cette semaine</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $jours = getJoursSemaine();
                        $jourActuel = getJourFrancais();
                        $travailleAujourdhui = false;
                        
                        foreach ($jours as $jour): 
                            $estPlanifie = false;
                            foreach ($planning as $p):
                                if ($p['jour_semaine'] === $jour):
                                    $estPlanifie = true;
                                    if ($jour === $jourActuel) $travailleAujourdhui = true;
                                    break;
                                endif;
                            endforeach;
                        ?>
                            <div class="col text-center <?= $jour === $jourActuel ? 'border border-2 border-success rounded' : '' ?>">
                                <div class="mb-2">
                                    <strong><?= ucfirst($jour) ?></strong>
                                    <?php if ($jour === $jourActuel): ?>
                                        <br><small class="text-success fw-bold">Aujourd'hui</small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($estPlanifie): ?>
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill text-muted"></i>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($travailleAujourdhui): ?>
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle"></i> Vous travaillez aujourd'hui !
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Vous ne travaillez pas aujourd'hui
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Signalements assignés -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-inbox"></i> Signalements en cours</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($signalementsAssignes)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Aucun signalement en cours</p>
                            <a href="<?= url('/chauffeur/signalements-disponibles') ?>" class="btn btn-primary">
                                Voir les signalements disponibles
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
                                    <?php foreach ($signalementsAssignes as $signalement): ?>
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
                                            <td><?= formatDate($signalement['created_at'], 'd/m/Y H:i') ?></td>
                                            <td>
                                                <a href="<?= url('/signalement/' . $signalement['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Voir
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
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
