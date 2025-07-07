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
    <title> Nombre d'employé</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <script src="../assets/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Nombre d'employé</h2>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Homme</th>
                <th>Femme</th>
                <th>Total</th>
                <th>Salaire moyen</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            $requete = "
                SELECT 
                SUM(CASE WHEN e.gender = 'M' THEN 1 ELSE 0 END) as nbh,
                SUM(CASE WHEN e.gender = 'F' THEN 1 ELSE 0 END) as nbf,
                COUNT(*) as total,
                AVG(CASE WHEN t.title LIKE '%Senior%' THEN s.salary ELSE NULL END) as salaire_senior,
                AVG(CASE WHEN t.title NOT LIKE '%Senior%' THEN s.salary ELSE NULL END) as salaire_simple
                FROM employees e
                JOIN dept_emp d ON e.emp_no = d.emp_no
                JOIN salaries s ON e.emp_no = s.emp_no
                JOIN titles t ON e.emp_no = t.emp_no
                WHERE d.dept_no = ?
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
                    $nbh = htmlspecialchars($employee['nbh']);
                    $nbf = htmlspecialchars($employee['nbf']);
                    $total = htmlspecialchars($employee['total']);
                    $salaire_simple = number_format($employee['salaire_simple'], 2, ',', ' ');
                    $salaire_senior = number_format($employee['salaire_senior'], 2, ',', ' ');

                    echo "<tr>";
                    echo "<td>$nbh</td>";
                    echo "<td>$nbf</td>";
                    echo "<td>$total</td>";
                    echo "<td>Simple: $salaire_simple <br> Senior: $salaire_senior</td>";
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
    <a href="../index.php" class="btn btn-secondary mt-3">Retour aux Départements</a>
</div>

</body>
</html>
