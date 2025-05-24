<!-- logout.php -->
<?php
    session_start(); // Inicia a sessão
    unset($_SESSION['email']); // Remove e-mail da sessão
    unset($_SESSION['senha']); // Remove senha da sessão
    header('Location: home.php'); // Redireciona para home