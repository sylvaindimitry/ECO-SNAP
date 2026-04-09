<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'ECO-SNAP' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= url($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand text-success fw-bold" href="<?= url('/') ?>">ECO-SNAP</a>
            
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/mission') ?>">Missions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/about') ?>">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/faq') ?>">FAQ</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars(userName()) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?= url('/dashboard') ?>">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <?php if (hasRole('chauffeur')): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('/chauffeur/dashboard') ?>">
                                            <i class="bi bi-truck"></i> Espace Chauffeur
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('/logout') ?>">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/login') ?>">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/register') ?>">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('/signalement/create') ?>" class="btn btn-success ms-3">
                        <i class="bi bi-exclamation-triangle"></i> SIGNALER
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="container mt-5 pt-5">
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="mt-5 pt-5">
        <?= $content ?? '' ?>
    </main>
    
    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p class="mb-0">© <?= date('Y') ?> ECO-SNAP. Tous droits réservés</p>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= url('assets/js/main.js') ?>"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= url($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
