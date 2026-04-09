<?php 
$pageTitle = 'À Propos - ECO-SNAP';
ob_start(); 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="text-center fw-bold text-success mb-4">
                        <i class="bi bi-info-circle"></i> À Propos d'ECO-SNAP
                    </h2>
                    
                    <div class="row align-items-center mb-5">
                        <div class="col-md-6">
                            <h3 class="fw-bold">Notre Mission</h3>
                            <p class="lead text-muted">
                                ECO-SNAP est une plateforme innovante qui connecte les citoyens engagés avec les équipes de collecte pour un environnement plus propre.
                            </p>
                            <p>
                                Notre objectif est de faciliter la signalisation des dépôts sauvages d'ordures et d'optimiser leur prise en charge en connectant automatiquement les signalements avec les équipes disponibles dans la zone géographique concernée.
                            </p>
                        </div>
                        <div class="col-md-6 text-center">
                            <i class="bi bi-recycle text-success" style="font-size: 10rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-5">
                        <div class="col-md-4 text-center mb-3">
                            <div class="mb-3">
                                <i class="bi bi-geo-alt text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Géolocalisation</h5>
                            <p class="text-muted">Chaque signalement est géolocalisé pour une intervention rapide et ciblée.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="mb-3">
                                <i class="bi bi-bell text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Notifications Temps Réel</h5>
                            <p class="text-muted">Les chauffeurs reçoivent instantanément les signalements de leur zone.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="mb-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Suivi Efficace</h5>
                            <p class="text-muted">Suivez l'évolution de chaque signalement jusqu'à sa résolution.</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-5">
                        <h3 class="fw-bold mb-4">Comment ça marche ?</h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-primary">
                                            <i class="bi bi-person"></i> Pour les Habitants
                                        </h5>
                                        <ol class="mb-0">
                                            <li class="mb-2">Créez un compte sur la plateforme</li>
                                            <li class="mb-2">Signalez un dépôt d'ordures avec photo et localisation</li>
                                            <li class="mb-2">Le système notifie automatiquement les équipes disponibles</li>
                                            <li>Suivez la résolution de votre signalement</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-success">
                                            <i class="bi bi-truck"></i> Pour les Chauffeurs/Équipes
                                        </h5>
                                        <ol class="mb-0">
                                            <li class="mb-2">Inscrivez-vous en précisant votre zone d'intervention</li>
                                            <li class="mb-2">Définissez vos jours de travail</li>
                                            <li class="mb-2">Recevez des notifications en temps réel pour votre zone</li>
                                            <li>Prenez en charge les signalements et marquez-les comme terminés</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h3 class="fw-bold mb-3">Pourquoi ECO-SNAP ?</h3>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3">
                                    <h2 class="text-success fw-bold">100%</h2>
                                    <p class="text-muted">Gratuit et accessible à tous</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3">
                                    <h2 class="text-success fw-bold">24/7</h2>
                                    <p class="text-muted">Disponible à tout moment</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3">
                                    <h2 class="text-success fw-bold">⚡ Rapide</h2>
                                    <p class="text-muted">Notifications instantanées</p>
                                </div>
                            </div>
                        </div>
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
