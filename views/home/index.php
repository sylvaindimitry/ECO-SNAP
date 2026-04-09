<?php 
$pageTitle = 'Accueil - ECO-SNAP';
ob_start(); 
?>

<!-- HERO SECTION -->
<section class="hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold mb-4">Localisez. Signalez. Protégez.</h1>
                <p class="lead mb-4">Chaque signalement compte pour un environnement sain. Ensemble, agissons pour un Cameroun plus propre.</p>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('/signalement/create') ?>" class="btn btn-success btn-lg me-3">
                        <i class="bi bi-exclamation-triangle"></i> Faire un signalement
                    </a>
                    <a href="<?= url('/dashboard') ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-speedometer2"></i> Mon Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= url('/register') ?>" class="btn btn-success btn-lg me-3">
                        <i class="bi bi-person-plus"></i> Rejoignez-nous
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= url('Photos/tof13.jpg') ?>" alt="ECO-SNAP" class="img-fluid rounded shadow-lg" 
                     onerror="this.src='https://via.placeholder.com/600x400?text=ECO-SNAP'">
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <div id="ecoCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?= url('Photos/tof13.jpg') ?>" class="d-block w-100 rounded" alt="Image 1"
                                 onerror="this.src='https://via.placeholder.com/600x400?text=Environnement+1'">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= url('Photos/tof10.jpg') ?>" class="d-block w-100 rounded" alt="Image 2"
                                 onerror="this.src='https://via.placeholder.com/600x400?text=Environnement+2'">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= url('Photos/tof1.jpg') ?>" class="d-block w-100 rounded" alt="Image 3"
                                 onerror="this.src='https://via.placeholder.com/600x400?text=Environnement+3'">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#ecoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ecoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3">Environnement durable pour un avenir toujours vert</h2>
                <p class="text-muted">Un environnement propre est un investissement pour un avenir durable. Ensemble, nous pouvons faire la différence.</p>
                
                <ul class="list-unstyled mb-4">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Avantages économiques</strong> - Réduire les coûts de nettoyage
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Environnement sain</strong> - Protéger notre planète
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Communauté active</strong> - Des citoyens engagés
                    </li>
                </ul>
                
                <a href="<?= url('/about') ?>" class="btn btn-success">
                    <i class="bi bi-info-circle"></i> À propos de nous
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES SECTION -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Les meilleurs services de nettoyage</h2>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm h-100 border-0">
                    <div class="text-center mb-3">
                        <i class="bi bi-recycle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-center">Recyclage efficace</h5>
                    <p class="text-muted text-center">Solutions de recyclage pour un environnement meilleur et plus durable.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm h-100 border-0">
                    <div class="text-center mb-3">
                        <i class="bi bi-tree text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-center">Environnement préservé</h5>
                    <p class="text-muted text-center">Construire un environnement propre et certain pour les générations futures.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm h-100 border-0">
                    <div class="text-center mb-3">
                        <i class="bi bi-droplet text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-center">Protéger l'Océan</h5>
                    <p class="text-muted text-center">Protéger la vie marine et les océans contre la pollution.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="py-5 bg-success text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Prêt à agir pour l'environnement ?</h2>
        <p class="lead mb-4">Rejoignez notre communauté et participez activement à la protection de l'environnement</p>
        
        <?php if (isLoggedIn()): ?>
            <a href="<?= url('/signalement/create') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-exclamation-triangle"></i> Faire un signalement
            </a>
        <?php else: ?>
            <a href="<?= url('/register') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-person-plus"></i> Créer un compte
            </a>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-section {
    background: linear-gradient(135deg, #2c5f2d 0%, #1e3c1f 100%);
}
</style>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 
?>
