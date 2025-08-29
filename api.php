<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado']);
    exit;
}

$acao = $_GET['acao'] ?? '';

if ($acao == 'atualizar') {
    $id = $_POST['id'];
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
    
    $sql = "UPDATE usuario SET nome='$nome', email='$email', data_nascimento='$data_nascimento' WHERE idusuario=$id";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} elseif ($acao == 'excluir') {
    $id = $_POST['id'];
    $sql = "DELETE FROM usuario WHERE idusuario=$id";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
?>