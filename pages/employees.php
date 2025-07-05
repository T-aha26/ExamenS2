<?php include "../inc/connexion.php"; ?>

<?php
if (!isset($connexion)) {
    die("Erreur : La connexion à la base de données a échoué.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employés du Département</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <script src="../assets/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Employés du Département</h2>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Sexe</th>
                <th>Date d'embauche</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            $requete = "
                SELECT e.emp_no, e.first_name, e.last_name, e.gender, e.hire_date
                FROM employees e
                JOIN dept_emp d ON e.emp_no = d.emp_no
                WHERE d.dept_no = ? AND d.to_date = '9999-01-01'
            ";

            $stmt = $connexion->prepare($requete);

            if (!$stmt) {
                echo "Erreur de préparation : " . $connexion->error;
                die("Erreur lors de la préparation de la requête.");
            }

            $stmt->bind_param("s", $code);

            if (!$stmt->execute()) {
                echo "Erreur d'exécution : " . $stmt->error;
                die("Erreur lors de l'exécution de la requête.");
            }

            $resultat = $stmt->get_result();

            if ($resultat->num_rows > 0) {
                while ($employee = $resultat->fetch_assoc()) {
                    $emp_no = $employee['emp_no'];
                    $prenom = htmlspecialchars($employee['first_name']);
                    $nom = htmlspecialchars($employee['last_name']);
                    $sexe = htmlspecialchars($employee['gender']);
                    $embauche = htmlspecialchars($employee['hire_date']);

                    echo "<tr>";
                    echo "<td><a href='fiche.php?id=$emp_no'>$nom</a></td>";
                    echo "<td>$prenom</td>";
                    echo "<td>$sexe</td>";
                    echo "<td>$embauche</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Aucun employé trouvé pour ce département.</td></tr>";
            }

            $stmt->close();
        } else {
            echo "<tr><td colspan='4' class='text-center text-danger'>Aucun département sélectionné.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <a href="../department.php" class="btn btn-secondary mt-3">Retour aux Départements</a>
</div>

</body>
</html>
