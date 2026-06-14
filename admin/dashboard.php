<?php
session_start();

if(!isset($_SESSION['id'])){
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link rel="stylesheet"
href="../assets/css/style.css">
</head>

<body>

<div class="container">

<div class="sidebar">

<div class="logo">
LMS
</div>

<ul>

<li>
<a href="#">Dashboard</a>
</li>

<li>
<a href="#">Modules</a>
</li>

<li>
<a href="#">Cours</a>
</li>

<li>
<a href="#">Enseignants</a>
</li>

<li>
<a href="#">Étudiants</a>
</li>

<li>
<a href="#">Certificats</a>
</li>

<li>
<a href="../logout.php">Déconnexion</a>
</li>

</ul>

</div>

<div class="main">

<div class="header">

<h1>Dashboard Administrateur</h1>

</div>

<div class="cards">

<div class="card">
<h3>Modules</h3>
<p>12</p>
</div>

<div class="card">
<h3>Cours</h3>
<p>28</p>
</div>

<div class="card">
<h3>Étudiants</h3>
<p>350</p>
</div>

<div class="card">
<h3>Enseignants</h3>
<p>20</p>
</div>

</div>

</div>

</div>

</body>
</html>