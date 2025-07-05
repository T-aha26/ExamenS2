<?php include "../inc/connexion.php"; ?>

<?php
$departement = $_GET['departement'] ?? '';
$nom = $_GET['nom'] ?? '';
$age_min = $_GET['age_min'] ?? '';
$age_max = $_GET['age_max'] ?? '';
$decalage = isset($_GET['decalage']) ? intval($_GET['decalage']) : 0;
$limite = 20;

$requete = "
    SELECT e.emp_no, e.first_name, e.last_name, e.birth_date
    FROM employees e
    JOIN dept_emp d ON e.emp_no = d.emp_no
    WHERE d.to_date = '9999-01-01'
";

$parametres = [];
$types = "";

if ($departement) {
    $requete .= " AND d.dept_no = ? ";
    $parametres[] = $departement;
    $types .= "s";
}

if ($nom) {
    $requete .= " AND e.last_name LIKE ? ";
    $parametres[] = "%$nom%";
    $types .= "s";
}

if ($age_min) {
    $requete .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= ? ";
    $parametres[] = intval($age_min);
    $types .= "i";
}

if ($age_max) {
    $requete .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= ? ";
    $parametres[] = intval($age_max);
    $types .= "i";
}

$requete .= " LIMIT ?, ? ";
$parametres[] = $decalage;
$parametres[] = $limite;
$types .= "ii";

$stmt = $connexion->prepare($requete);
$stmt->bind_param($types, ...$parametres);
$stmt->execute();
$resultat = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche d'employés</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.5-dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Recherche d'employés</h2>
    
    <form method="GET" class="row g-3 mb-4">
        <div class="col">
            <input type="text" name="departement" class="form-control" placeholder="Département" value="<?= htmlspecialchars($departement) ?>">
        </div>
        <div class="col">
            <input type="text" name="nom" class="form-control" placeholder="Nom de famille" value="<?= htmlspecialchars($nom) ?>">
        </div>
        <div class="col">
            <input type="number" name="age_min" class="form-control" placeholder="Âge minimum" value="<?= htmlspecialchars($age_min) ?>">
        </div>
        <div class="col">
            <input type="number" name="age_max" class="form-control" placeholder="Âge maximum" value="<?= htmlspecialchars($age_max) ?>">
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($employe = $resultat->fetch_assoc()) : ?>
            <tr>
                <td><a href="fiche.php?id=<?= $employe['emp_no'] ?>"><?= htmlspecialchars($employe['last_name']) ?></a></td>
                <td><?= htmlspecialchars($employe['first_name']) ?></td>
                <td><?= htmlspecialchars($employe['birth_date']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <?php
        $parametresUrl = http_build_query([
            'departement' => $departement,
            'nom' => $nom,
            'age_min' => $age_min,
            'age_max' => $age_max
        ]);
        ?>
        <a href="?<?= $parametresUrl ?>&decalage=<?= max(0, $decalage - $limite) ?>" class="btn btn-secondary">Précédent</a>
        <a href="?<?= $parametresUrl ?>&decalage=<?= $decalage + $limite ?>" class="btn btn-secondary">Suivant</a>
    </div>
</body>
</html>
