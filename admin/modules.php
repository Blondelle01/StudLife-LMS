<?php
include("../config/database.php");

if(isset($_POST['save'])){

$titre=$_POST['titre'];
$description=$_POST['description'];

$sql="INSERT INTO modules(titre,description)
VALUES('$titre','$description')";

$conn->query($sql);
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Modules</title>
</head>

<body>

<h2>Créer un module</h2>

<form method="POST">

<input type="text"
name="titre"
placeholder="Titre du module"
required>

<br><br>

<textarea
name="description"
placeholder="Description">
</textarea>

<br><br>

<button name="save">
Enregistrer
</button>

</form>

</body>

</html>