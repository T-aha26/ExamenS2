<?php include "../inc/connexion.php"; ?>

<?php
if (!isset($connexion)) {
    die("Erreur : La connexion à la base de données a échoué.");
}

if (isset($_GET['id'])) {
    $emp_no = intval($_GET['id']);

    $requete = "
        SELECT e.emp_no, e.first_name, e.last_name, e.gender, e.hire_date, t.title
        FROM employees e
        JOIN titles t ON e.emp_no = t.emp_no
        WHERE e.emp_no = ? AND t.to_date = '9999-01-01'
    ";

    $stmt = $connexion->prepare($requete);
    $stmt->bind_param("i", $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $employe = $result->fetch_assoc();

    if (!$employe) {
        die("Employé non trouvé.");
    }
} else {
    die("Aucun employé sélectionné.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Employé</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.5-dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Fiche de l'employé</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($employe['last_name']) ?></li>
        <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($employe['first_name']) ?></li>
        <li class="list-group-item"><strong>Sexe :</strong> <?= htmlspecialchars($employe['gender']) ?></li>
        <li class="list-group-item"><strong>Date d'embauche :</strong> <?= htmlspecialchars($employe['hire_date']) ?></li>
        <li class="list-group-item"><strong>Poste actuel :</strong> <?= htmlspecialchars($employe['title']) ?></li>
    </ul>

    <h4>Historique des salaires</h4>
    <table class="table table-bordered">
        <thead>
            <tr><th>Salaire</th><th>De</th><th>À</th></tr>
        </thead>
        <tbody>
        <?php
        $salaireReq = "SELECT salary, from_date, to_date FROM salaries WHERE emp_no = ? ORDER BY from_date DESC";
        $stmt = $connexion->prepare($salaireReq);
        $stmt->bind_param("i", $emp_no);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($salaire = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($salaire['salary']) . " €</td>
                    <td>" . htmlspecialchars($salaire['from_date']) . "</td>
                    <td>" . htmlspecialchars($salaire['to_date']) . "</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>

    <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
</body>
</html>
