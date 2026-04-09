<?php
require_once "BDconnexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Récupération
    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sécuriser mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si email existe
    $sql_check = "SELECT matricule FROM habitant WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        echo "Email déjà utilisé ";
    } else {

        // Insertion
        $sql = "INSERT INTO habitant (matricule, nom, prenom, telephone, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "ssssss", $matricule, $nom, $prenom, $telephone, $email, $password_hash);

        if (mysqli_stmt_execute($stmt)) {
            echo "Inscription réussie ✅";
        } else {
            echo "Erreur : " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
    header("Location: login.php");

    mysqli_stmt_close($stmt_check);
    mysqli_close($conn);
}
?>