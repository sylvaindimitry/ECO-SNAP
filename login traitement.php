<?php  include_once 'BDconnexion.php' ?> 
<?php
session_start();
require_once "BDconnexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Rechercher l'utilisateur
    $sql = "SELECT matricule, nom, password FROM habitant WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // Vérification du mot de passe
        if (password_verify($password, $row['password'])) {

            // Créer session
            $_SESSION['matricule'] = $row['matricule'];
            $_SESSION['nom'] = $row['nom'];

            echo "Connexion réussie ";

            // Redirection (optionnel)
            header("Location: index.php");
        } else {
            
            echo "<script>
                    alert('Mot de passe incorrect ');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "Email introuvable ";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>