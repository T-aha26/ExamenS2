<?php include "inc/connexion.php"; ?>

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
    <title>Départements</title>

  
    <link rel="stylesheet" href="assets/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <script src="assets/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Liste des Départements</h2>
    <form method="GET" action="pages/employees.php" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="departement" class="form-control" placeholder="Département">
    </div>
    <div class="col-md-3">
        <input type="text" name="nom" class="form-control" placeholder="Nom de famille">
    </div>
    <div class="col-md-2">
        <input type="number" name="age_min" class="form-control" placeholder="Âge min">
    </div>
    <div class="col-md-2">
        <input type="number" name="age_max" class="form-control" placeholder="Âge max">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Rechercher</button>
    </div>
</form>


    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nom du Département</th>
                <th>Manager</th>
                <th>Voir les Employés</th>
                <th>Nombre Employé</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $requete = "
            SELECT d.dept_no, d.dept_name, e.first_name, e.last_name
            FROM departments d
            JOIN dept_manager m ON d.dept_no = m.dept_no
            JOIN employees e ON m.emp_no = e.emp_no
            WHERE m.to_date = '9999-01-01'
        ";

        $liste = $connexion->query($requete);

        if ($liste) {
            foreach ($liste as $departement) {
                $code = $departement['dept_no'];
                $nom = $departement['dept_name'];
                $manager = $departement['first_name'] . " " . $departement['last_name'];
                
                echo "<tr>"; 
                echo "<td>$nom</td>";
                
                echo "<td>$manager</td>";
                echo "<td><a href='pages/employees.php?code=$code' class='btn btn-primary btn-sm'>Voir</a></td>";
                echo "<td><a href='pages/nbemployees.php?code=$code' class='btn btn-primary btn-sm'>Voir</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3' class='text-center text-danger'>Aucun département trouvé ou erreur de requête.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
