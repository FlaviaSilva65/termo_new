<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Redimensionar</title>
</head>

<body>
    <?= $this->Html->css(['croppie']) . $this->Html->script(['croppie.min', 'custom_alterado']) ?>
    <?= $this->Form->create($usuario, ['autocomplete' => 'off', 'type' => 'file', 'novalidate']) ?>
    <h1>Formulário</h1>

    <!-- Campo upload -->
    <label>Foto: </label>

    

    <input type="file" name="imagem" id="imagem" accept="image/*"><br><br>

    <!-- Botão recortar imagem e realizar upload -->
    <button class="btn-upload-imagem">Enviar</button>

    <!-- Apresentar o preview da imagem -->
    <div id="preview"></div>

    <!-- <script src="js/custom.js"></script> -->

    <div>
        <input id="urlenviada" value="<?= $this->Url->build(['controller' => 'Usuarios', 'action' => 'upload']) ?>" class="d-none">
        
    </div>

    <?= $this->Form->end() ?>

</body>

</html>