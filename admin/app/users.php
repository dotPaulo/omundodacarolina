<?php


require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../../app/helpers/JwtHelper.php';

include (__DIR__ . '/../../app/database/db.php');


$username = $email = $password = $confirm_password = $role = "";
$username_err = $email_err = $password_err = $confirm_password_err = $login_err = $role_err = "";

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

    if (empty(trim($_POST["role"]))) {
        $role_err = "Escolha um cargo.";
    } else {
        $role = trim($_POST["role"]);
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'role' => $role
        ];

        if (create('utilizadores', $data)) {
            header("location: Vutilizadores.php");
            exit();
        } else {
            header("Location: /../../error_page.php");
        }
    }
}

if (isset($_POST['update-btn-utilizadores'])) {
    $id = isset($_POST["id"]) ? trim($_POST["id"]) : null;
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : null;
    $role = isset($_POST["role"]) ? trim($_POST["role"]) : null;

    if ($username === null || $username === '') {
        $username_err = "O nome não pode estar vazio.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $username_err = "O nome só pode conter letras, números, e underscores.";
    }

    if ($email === null || $email === '') {
        $email_err = "O email não pode estar vazio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }

    if ($role === null || $role === '') {
        $role_err = "O cargo não pode estar vazio.";
    } elseif (!in_array($role, ['admin', 'user'])) {
        $role_err = "Cargo inválido.";
    }

    if (empty($username_err) && empty($email_err) && empty($role_err)) {
        if (!$id) {
            die("No user ID provided for update");
        }

        $conditions = ['id' => $id];
        $user = selectOne('utilizadores', $conditions);

        if (!$user) {
            die("User not found for update");
        }

        $update_data = [
            'username' => $username,
            'email' => $email,
            'role' => $role,
        ];

        if ($password !== null && $password !== '') {
            $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $result = update('utilizadores', $id, $update_data);

        if ($result) {
            $current_user = null;
            if (isset($_COOKIE['jwt'])) {
                $decoded = validateToken($_COOKIE['jwt'], $key);
                if ($decoded) {
                    $current_user = selectOne('utilizadores', ['email' => $decoded->email]);
                }
            }

            if ($current_user && $current_user['id'] == $id) {

                $jwt = generateToken($id, $username, $email, $role, $key);
                setcookie("jwt", $jwt, [
                    'expires' => time() + 3600,
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Strict',
                    'secure' => true
                ]);
            }

            header("Location: Vutilizadores.php");
            exit();
        } else {
            $update_err = "Failed to update user.";
        }
    }
}




if (isset($_POST['delete-btn'])) {
    if (!isset($_COOKIE['jwt'])) {
        header("Location: ../login.php");
        exit();
    }

    $jwt = $_COOKIE['jwt'];
    if (isTokenExpired($jwt, $key) || !validateToken($jwt, $key)) {
        clearAuthCookies();
        header("Location: ../login.php");
        exit();
    }

    $id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null) {
        $_SESSION['error'] = "ID de utilizador inválido.";
        header("Location: ../Vutilizadores.php");
        exit();
    }

    $decoded = validateToken($jwt, $key);
    if ($id == $decoded->id) {
        $_SESSION['error'] = "Não pode apagar o seu próprio utilizador.";
        header("Location: ../Vutilizadores.php");
        exit();
    }

    try {
        $result = delete('utilizadores', $id);

    } catch (Exception $e) {
        $_SESSION['error'] = "Erro ao eliminar o utilizador.";
    }

    header("Location: ../Vutilizadores.php");
    exit();
}

$nome = $slug = $descricao = $meta_titulo = $meta_descricao = $imagem = "";
$nome_err = $slug_err = $descricao_err = $meta_titulo_err = $meta_descricao_err = $imagem_err = "";


if (isset($_POST["add-post-btn"])) {
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Insira um nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    if (empty(trim($_POST["descricao"]))) {
        $descricao_err = "Escreva algo na descrição.";
    } else {
        $descricao = $_POST["descricao"];
    }

    if (empty($_FILES["imagem"]["name"])) {
        $imagem_err = "Insira uma imagem.";
    } else {
        $imagem = $_FILES["imagem"]["name"];
        $imagem_tmp = $_FILES["imagem"]["tmp_name"];
        $imagem_extension = pathinfo($imagem, PATHINFO_EXTENSION);
        $filename = time() . "." . $imagem_extension;

        $upload_dir = dirname(__DIR__, 2) . "/Upload/publicacoes/";
        $target_file = $upload_dir . $filename;

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($imagem_tmp, $target_file)) {
        } else {
            $imagem_err = "Falha ao fazer upload da imagem.";
        }
    }


    if (empty(trim($_POST["public_type"]))) {
        $public_type_err = "Escolha um tipo de publicação.";
    } else {
        $public_type = trim($_POST["public_type"]);
    }


    $meta_titulo = trim($_POST["meta_titulo"]);
    $meta_descricao = trim($_POST["meta_descricao"]);

    if (empty($nome_err) && empty($descricao_err) && empty($categoria_err)) {

        $relative_path = "Upload/publicacoes/" . $filename;

        $data = [
            'public_type' => $public_type,
            'nome' => $nome,
            'descricao' => $descricao,
            'imagem' => $relative_path,
            'meta_titulo' => $meta_titulo,
            'meta_descricao' => $meta_descricao
        ];

        if (create('publicacoes', $data)) {
            header("location: ../Vpublicacoes.php");
            exit();
        } else {
            header("Location: /../../error_page.php");
        }
    }
}

if (isset($_POST['update-post-btn'])) {
    $id = isset($_POST["post_id"]) ? trim($_POST["post_id"]) : null;
    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : null;
    $descricao = isset($_POST["descricao"]) ? trim($_POST["descricao"]) : null;
    $public_type = isset($_POST["public_type"]) ? trim($_POST["public_type"]) : null;
    $meta_titulo = isset($_POST["meta_titulo"]) ? trim($_POST["meta_titulo"]) : null;
    $meta_descricao = isset($_POST["meta_descricao"]) ? trim($_POST["meta_descricao"]) : null;

    if (!$id) {
        $_SESSION['message'] = "Não foram feitas alterações";
        header("Location: Vpublicacoes.php");
        exit();
    }

    $current_post = selectOne('publicacoes', ['id' => $id]);

    if (!$current_post) {
        $_SESSION['message'] = "A publicação com esse ID não existe";
        header("Location: Vpublicacoes.php");
        exit();
    }

    $changes_made = false;
    $data = [];

    if ($nome !== $current_post['nome']) {
        $data['nome'] = $nome;
        $changes_made = true;
    }
    if ($descricao !== $current_post['descricao']) {
        $data['descricao'] = $descricao;
        $changes_made = true;
    }
    if ($public_type !== $current_post['public_type']) {
        $data['public_type'] = $public_type;
        $changes_made = true;
    }
    if ($meta_titulo !== $current_post['meta_titulo']) {
        $data['meta_titulo'] = $meta_titulo;
        $changes_made = true;
    }
    if ($meta_descricao !== $current_post['meta_descricao']) {
        $data['meta_descricao'] = $meta_descricao;
        $changes_made = true;
    }

    if (!empty($_FILES['imagem']['name'])) {
        $imagem = $_FILES["imagem"]["name"];
        $imagem_tmp = $_FILES["imagem"]["tmp_name"];
        $imagem_extension = pathinfo($imagem, PATHINFO_EXTENSION);
        $filename = time() . "." . $imagem_extension;

        $upload_dir = dirname(__DIR__, 2) . "/Upload/publicacoes/";
        $target_file = $upload_dir . $filename;

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($imagem_tmp, $target_file)) {
            $data['imagem'] = "Upload/publicacoes/" . $filename;
            $changes_made = true;

  
            if (!empty($current_post['imagem'])) {
                $old_image_path = dirname(__DIR__, 2) . '/' . $current_post['imagem'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        } else {
            $_SESSION['message'] = "Falha ao fazer upload da nova imagem.";
            header("Location: Vpublicacoes.php");
            exit();
        }
    }

    if ($changes_made) {
        $result = update('publicacoes', $id, $data);

        if ($result) {
            $_SESSION['message'] = "Publicação atualizada com sucesso.";
        } else {
            $_SESSION['message'] = "Erro ao atualizar a publicação.";
        }
    } else {
        $_SESSION['message'] = "Não foram feitas alterações";
    }

    header("Location: Vpublicacoes.php");
    exit();
}

if (isset($_POST['delete-btn-post'])) {
    if (!isset($_COOKIE['jwt'])) {
        header("Location: ../login.php");
        exit();
    }

    $jwt = $_COOKIE['jwt'];
    if (isTokenExpired($jwt, $key) || !validateToken($jwt, $key)) {
        clearAuthCookies();
        header("Location: ../login.php");
        exit();
    }

    $id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null) {
        $_SESSION['error'] = "ID de publicação inválido.";
        header("Location: ../Vpublicacoes.php");
        exit();
    }

    try {
  
        $post = selectOne('publicacoes', ['id' => $id]);

        if ($post) {
   
            if (!empty($post['imagem'])) {
                $image_path = dirname(__DIR__, 2) . '/' . $post['imagem'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }


            $result = delete('publicacoes', $id);

            if ($result) {
                $_SESSION['success'] = "Publicação eliminada com sucesso.";
            } else {
                $_SESSION['error'] = "Erro ao eliminar a publicação.";
            }
        } else {
            $_SESSION['error'] = "Publicação não encontrada.";
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Erro ao eliminar a publicação: " . $e->getMessage();
    }

    header("Location: ../Vpublicacoes.php");
    exit();
}

?>