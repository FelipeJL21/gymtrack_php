<?php
include('proteger.php');
include('conexao.php');

$sql = "SELECT *, DATE_FORMAT(data_nascimento, '%d/%m/%Y') as data_formatada FROM usuario";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Gymtrack</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Painel Gymtrack</a>
            <span class="navbar-text text-white">
                Bem-vindo(a), <?php echo $_SESSION['nome']; ?>!
            </span>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Gerenciamento de Usuários</h2>
        <form class="form-inline mb-3">
            <input class="form-control mr-sm-2" type="search" id="pesquisa" placeholder="Pesquisar por nome ou e-mail" style="width: 100%;">
        </form>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Data de Nascimento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-usuarios">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr id="usuario-<?php echo $row['idusuario']; ?>">
                    <td><?php echo $row['idusuario']; ?></td>
                    <td data-field="nome"><?php echo $row['nome']; ?></td>
                    <td data-field="email"><?php echo $row['email']; ?></td>
                    <td data-field="data_nascimento" data-value="<?php echo $row['data_nascimento']; ?>"><?php echo $row['data_formatada']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-editar" data-id="<?php echo $row['idusuario']; ?>">Editar</button>
                        <button class="btn btn-sm btn-success btn-salvar" data-id="<?php echo $row['idusuario']; ?>" style="display:none;">Salvar</button>
                        <button class="btn btn-sm btn-danger btn-excluir" data-id="<?php echo $row['idusuario']; ?>">Excluir</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function() {
        // Pesquisa em tempo real
        $("#pesquisa").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tabela-usuarios tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Botão EDITAR
        $(document).on('click', '.btn-editar', function() {
            var id = $(this).data('id');
            var linha = $('#usuario-' + id);
            linha.find('.btn-editar').hide();
            linha.find('.btn-salvar').show();

            linha.find('td[data-field="nome"], td[data-field="email"]').each(function() {
                var valor = $(this).text();
                $(this).html('<input type="text" class="form-control" value="' + valor + '">');
            });
            var dataNasc = linha.find('td[data-field="data_nascimento"]').data('value');
            linha.find('td[data-field="data_nascimento"]').html('<input type="date" class="form-control" value="' + dataNasc + '">');
        });

        // Botão SALVAR
        $(document).on('click', '.btn-salvar', function() {
            var id = $(this).data('id');
            var linha = $('#usuario-' + id);
            var dados = {
                id: id,
                nome: linha.find('td[data-field="nome"] input').val(),
                email: linha.find('td[data-field="email"] input').val(),
                data_nascimento: linha.find('td[data-field="data_nascimento"] input').val()
            };

            $.post('api.php?acao=atualizar', dados, function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    linha.find('.btn-editar').show();
                    linha.find('.btn-salvar').hide();
                    linha.find('td[data-field="nome"]').text(dados.nome);
                    linha.find('td[data-field="email"]').text(dados.email);
                    
                    // Formata a data para dd/mm/yyyy
                    var dataObj = new Date(dados.data_nascimento + 'T00:00:00');
                    var dia = String(dataObj.getDate()).padStart(2, '0');
                    var mes = String(dataObj.getMonth() + 1).padStart(2, '0');
                    var ano = dataObj.getFullYear();
                    var dataFormatada = dia + '/' + mes + '/' + ano;

                    var dataCell = linha.find('td[data-field="data_nascimento"]');
                    dataCell.text(dataFormatada);
                    dataCell.data('value', dados.data_nascimento);
                } else {
                    alert('Erro ao atualizar: ' + res.message);
                }
            });
        });

        // Botão EXCLUIR
        $(document).on('click', '.btn-excluir', function() {
            if (confirm('Tem certeza que deseja excluir este usuário?')) {
                var id = $(this).data('id');
                $.post('api.php?acao=excluir', { id: id }, function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#usuario-' + id).remove();
                    } else {
                        alert('Erro ao excluir: ' + res.message);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
