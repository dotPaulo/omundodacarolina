<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

include (__DIR__ . '/../database/db.php');

require_once __DIR__ . '/../helpers/JwtHelper.php';


$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = $login_err = "";

if (isset($_POST['registo-btn'])) {
    unset($_POST['registo-btn']);

    if (empty(trim($_POST["username"]))) {
        $username_err = "Insira um nome de utilizador.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Alguns caracteres não podem ser utilizados.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Insira um email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Formato de email inválido.";
    } else {
        $email = trim($_POST["email"]);

        $conditions = ['email' => $email];
        $existing_user = selectOne('utilizadores', $conditions);

        if ($existing_user) {
            $email_err = "Este email já está em uso.";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Introduza uma palavra-passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor confirme a palavra-passe.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "As palavra-passes não coincidem.";
        }
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'role' => 'user'
        ];

        if (create('utilizadores', $data)) {
            header("location: login.php");
            exit();
        } else {
            header("Location: /../error_page.php");
        }
    }
}

if (isset($_POST['login-btn'])) {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Insira um email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Insira uma palavra-passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $conditions = ['email' => $email];
        $user = selectOne('utilizadores', $conditions);

        if (is_array($user) && password_verify($password, $user['password'])) {

            $id = $user['id'];
            $username = strval($user['username']);
            $email = strval($user['email']);
            $role = strval($user['role']);

            $jwt = generateToken($id, $username, $email, $role, $key);

            setcookie("jwt", $jwt, [
                'expires' => time() + 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
                'secure' => true
            ]);

            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $login_err = "Email ou palavra-passe incorretos.";
        }
    }
}

?>