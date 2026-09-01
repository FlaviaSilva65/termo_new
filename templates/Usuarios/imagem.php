<html>

<head>
    <title>How to Crop Image using jQuery</title>

    <?= $this->Html->css(['jquery.Jcrop.min']) ?>
    <?= $this->Html->script(['jquery.min', 'jquery.Jcrop.min', 'function']) ?>


</head>

<body>
    <div class="col-12 mt-2 mb-5 py-0 d-flex">
        <div class="col-4 ">

            <!-- Futuramente exibir esse botão para fazer o upload da imagem -->
            <?php $this->Form->control('arquivo', [
                'class' => 'img py-0',
                'type' => 'file',
                'id' => 'imagem',
                'label' => ['text' => 'Anexar Assinatura', 'class' => 'btn btn-secondary btn-upload-imagem'],
                'style' => 'position: absolute; opacity: 0;'
            ]); ?>

            <img src="../img/original-image.jpeg" id="cropbox" class="img" /><br />

        </div>
    </div>
    <div id="btn">
        <input type="button" id="crop" value="CROP">
    </div>

    <div>
        <img src="#" id="cropped_img" style="display: none;">
    </div>

    <div id="mensagens"></div>

    <div>
        <input id="urlupload" value="<?= $this->Url->build(['controller' => 'Usuarios', 'action' => 'upload']) ?>" class="d-none">
    </div>
 

</body>

</html>
<script>
    $("#crop").click(function () {
        var img = $("#cropbox").attr('src');
        // var upload = document.getElementById('urlupload').value;
        // alert(upload);
        // $("#cropped_img").show();
        // $("#cropped_img").attr('src', 'image-crop.php?x=' + size.x + '&y=' + size.y + '&w=' + size.w + '&h=' + size.h + '&img=' + img);

        $.ajax({
            method: 'post',
            url: "<?php echo $this->Url->build(['controller' => 'Usuarios', 'action' => 'upload']); ?>",
           
            data: { status: 'ok'},
            success: function(data){
                $("#mensagens").html('Consegui');
            },
            error: function(){
                $("#mensagens").html("Tivemos um erro ao salvar.");
            }
        });
    });
</script>