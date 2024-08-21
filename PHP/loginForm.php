<?php
// Inclure le fichier de connexion à la base de données et les fonctions de logging
require_once('db.php');
include('logs.php');

$errorInfo = false;

// Vérifier si la connexion à la base de données est réussie
if (!$dbh) {
    die('Connexion à la base de données échouée.');
}

// Vérifier si le formulaire a été soumis
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Sanitize les données de l'utilisateur
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Préparer la requête pour récupérer l'utilisateur par email
    $loginSql = 'SELECT * FROM users WHERE email = :email';
    $preparedLoginRequest = $dbh->prepare($loginSql);

    if (!$preparedLoginRequest) {
        die('Erreur lors de la préparation de la requête SQL : ' . implode(", ", $dbh->errorInfo()));
    }

    // Exécuter la requête
    if (!$preparedLoginRequest->execute(['email' => $email])) {
        die('Erreur lors de l\'exécution de la requête SQL : ' . implode(", ", $preparedLoginRequest->errorInfo()));
    }

    // Récupérer l'utilisateur depuis la base de données
    $user = $preparedLoginRequest->fetch(PDO::FETCH_ASSOC);
    var_dump($user); // Affiche les informations utilisateur pour debugging

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user) {
        if (password_verify($password, $user['password'])) {
            session_start();
            // Stocker les informations utilisateur dans la session
            $_SESSION['userId'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['user'] = $user;
            $_SESSION['theme'] = $user['theme'] ?? 'default'; // Si le champ theme existe

            insert_logs('connexion');
            header('location:../index.php'); // Rediriger vers la page d'accueil
            exit;
        } else {
            $errorInfo = true;
        }
    } else {
        $errorInfo = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="CSS/form.css">
</head>
<body>
    <form action="" method="POST">
        <h1>Inscription</h1>
        
        <input type="text" id="firstname" name="firstname" placeholder="Prénom" required>
        <input type="text" id="lastname" name="lastname" placeholder="Nom" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Mot de passe" required>
        
        <div id="radio">
            <input type="radio" name="gender" value="male" required> Homme
            <input type="radio" name="gender" value="female" required> Femme
        </div>
        
        <div id="captcha-box">
            <input type="text" name="captcha_answer" placeholder="Entrez le CAPTCHA" required>
          
        </div>

        <input type="hidden" name="captcha_id" value="<?php echo $captcha_id; ?>">

        <button type="submit" class="btn">S'inscrire</button>
    </form>
</body>
</html>
