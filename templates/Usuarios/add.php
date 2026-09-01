<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">

<?= $this->Html->script(['custom']) ?>
<div class="col-6 mx-auto">

    <?= $this->Form->create($usuario, ['accept' => '.jpg', 'enctype' => 'multipart/form-data', 'novalidate']) ?>

    <div class="col-12 d-flex">
        <div class="col-2 pe-2">

            <?= $this->Form->control("cd_rf", [
                "class" => "col-2",
                "type" => "number",
                "onchange" => "busca_dados(this)",
                "label" => "RF"
            ]); ?>
        </div>
        <div class="col-6 ps-2">

            <?= $this->Form->control('nm_usuario', ['type' => 'text', 'class' => 'col-8', 'label' => 'Nome']); ?>
        </div>

    </div>
    <div class="col-12 d-flex">

        <div class="col-3 pe-2">
            <?= $this->Form->control('cd_cpf', ['class' => 'cpf', 'label' => 'CPF']); ?>
        </div>

        <div class="col-5 ps-2">
            <?= $this->Form->control('email', ['label' => 'E-mail']); ?>
        </div>

    </div>
    <div class="col-12 d-flex mb-3">

        <div class="col-4 pe-2">
            <?= $this->Form->control('password', ['label' => 'Senha']); ?>
        </div>
        <div class="col-4 ps-2">
            <?= $this->Form->control('tp_usuarios_id', ['label' => 'Grupo de Usuário', 'empty' => '--- ', 'options' => $tp_usuarios, 'onchange' => 'exibe_escola(this)']); ?>

        </div>

    </div>
    <div class="col-12 d-flex " id="nm_escola">
        <div class="col-8">
            <?= $this->form->control('nm_unid_escolar', ['placeholder' => 'Digite o nome da Escola', 'id' => 'escola', 'label' => 'Escola']); ?>
        </div>
    </div>
    <div class="col-8 d-flex mt-3">
        <div class="navbar navbar-light" style="background-color: #e3f2fd;">
            <div class="container-fluid">Selecione a Imagem</div>
            <div align="center">
                <?=
                $this->Html->image($assinatura->imagem ?? 'sf_3x2.jpg', [
                    'class' => 'w-25 cursor-pointer shadow',
                    'id' => 'img_foto',
                    'onClick' => 'upload_imagem.click()',
                    'title' => 'Clique para selecionar uma imagem !'
                ]) .
                    '<input type="file" name="upload_imagem" id="upload_imagem" style="display:none;" accept="image/*">' .
                    $this->Form->control('imagem', ['label' => '', 'class' => 'd-none'])
                ?>
                <br />
                <div id="uploaded_image"></div>
            </div>

        </div>
    </div>

    <?= $this->Form->button('<i class="fas fa-check mr-2"></i>Concluir', ['type' => 'submit', 'class' => 'btn btn-sm btn-success mr-3', 'escapeTitle' => false]) ?>

    <!-- Botão recortar imagem e realizar upload -->

    <div class="col-4 mt-4">
        <?= $this->Form->control('ic_ativo', ['type' => 'checkbox', 'class' => 'me-3', 'label' => ['text' => 'Ativo']]); ?>

    </div>



    <div class="row my-4 mx-0 d-flex justify-content-center">
        <div class="col-9">
            <div class="d-flex justify-content-center mt-4">
                <button class="btn btn-warning px-4 me-2" onclick="history.back()">
                    <i class="bi bi-arrow-return-left text-white"></i>
                </button>

                <button class="btn btn-success px-4 ms-2" onclick="return ver_escola()" type="submit"><i class="bi bi-save"></i> </button>
            </div>
        </div>
    </div>

    <div>
        <input id="urlupload" value="<?= $this->Url->build(['controller' => 'Usuarios', 'action' => 'upload']) ?>" class="d-none">
    </div>

    <?= $this->Form->end() ?>

    <div id="uploadimageModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Corte a Imagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div id="image_demo" style="width:300px; margin-top: 30px"></div>
                        </div>
                        <div class="col-md-2" style="padding-top: 30px;">
                            <button class="btn btn-success crop_imagem">Corte</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>

    function ver_escola() {
        var escola = document.getElementById('nomeList').value;

        if (escola.length === 0) {

            document.getElementById('nomeList').focus();
            return false;

        }
        
    }

    async function busca_dados(el) {

        if (!el) {
            return;
        }

        try {
            const response = await fetch(`<?php echo $this->Url->build(['controller' => 'Usuarios', 'action' => 'carregarRf']); ?> ?rf=${el.value}`, {

                headers: {

                    'X-Requested-With': 'XMLHttpRequest'
                },

            });

            const resultado = await response.json();

            if (resultado.success) {
                document.getElementById('nm-usuario').value = resultado.data.name;
                document.getElementById('cd-cpf').value = resultado.data.tax;
                document.getElementById('email').value = resultado.data.email;

                document.getElementById('escola').value = resultado.data.places.shift();

                console.log('Recebido com sucesso:', resultado.mensagem);
            } else {
                alert('Erro ao atualizar: ' + resultado.mensagem);
            }
        } catch (erro) {
            console.error('Erro na comunicação com o servidor:', erro);
        }
    }


    function exibe_escola(el) {

        var elemento = el.value;
        // alert (elemento);
        if (elemento == 1 || elemento == 5) {
            var nm_escola = document.getElementById('nm_escola').classList.remove("d-none");
        } else {
            var nm_escola = document.getElementById('nm_escola').classList.add("d-none");
        }

    }
</script>