
 <?php  include_once 'BDconnexion.php' ?> 
 <!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription ECO-SNAP</title>
<link rel="stylesheet" href="register style.css">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
  
<div class="glass-card text-center">
    <h3 class="mb-4">Inscription</h3>
    <form action="traitement register.php" method="post">
        <div class="mb-3">
            <input type="text" class="form-control" name="nom" id="nom"  placeholder="Nom" required>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Prénom" >
        </div>
        <div class="mb-3">
            <input type="tel" class="form-control"  name="telephone" id="telephone" placeholder="Téléphone" required>
        </div>
        <div class="mb-3">
            <input type="email" class="form-control"name="email" id="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn btn-glass w-100">S'inscrire</button>
    </form>
    <p class="mt-3">Déjà un compte ? <a href="login.php" class="text-white">Connectez-vous</a></p>
    ECO-SNAP
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</body>
</html>
