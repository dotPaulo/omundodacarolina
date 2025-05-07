<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$config = require_once 'JWTconfig.php';
$key = $config['jwt_secret'];

function generateToken($id, $username, $email, $role, $key)
{
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;
    $issuer = "https://omundodacarolina.pt/";

    $payload = array(
        'iss' => $issuer,
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'role' => $role
    );

    return JWT::encode($payload, $key, 'HS256');
}

function validateToken($token, $key)
{
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        error_log('JWT decoding failed: ' . $e->getMessage());
        return false;
    }
}

function isTokenExpired($token, $key)
{
    $decoded = validateToken($token, $key);
    if ($decoded && isset($decoded->exp)) {
        return time() >= $decoded->exp;
    }
    return true;
}

function clearAuthCookies()
{
    setcookie('token', '', time() - 3600, '/', '', true, true);
}

function handleTokenExpiration($token, $key)
{
    if (isTokenExpired($token, $key)) {
        clearAuthCookies();
        header('Location: ./login.php');
        exit;
    }
}

function ensureValidToken()
{
    global $key;

    if (!isset($_COOKIE['token'])) {
        header('Location: ./login.php');
        exit;
    }

    $token = $_COOKIE['token'];
    handleTokenExpiration($token, $key);

}

function getUserIdFromJWT($token, $key)
{
    $decoded = validateToken($token, $key);
    if ($decoded && isset($decoded->id)) {
        return $decoded->username;
    }
    return null;
}
?>