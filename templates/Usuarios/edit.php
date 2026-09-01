<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">

<?= $this->Html->script(['custom']) ?>
<div class="col-6 mx-auto">
  <?= $this->Form->create($pessoa, ['accept' => '.jpg', 'enctype' => 'multipart/form-data', 'novalidate']) ?>
  <?php // $this->Form->create(null, ['accept' => '.jpg', 'type' => 'file', 'novalidate']) ?>

  <div class="col-12 d-flex">
    <div class="col-2 pe-2">
      <?= $this->Form->control('cd_rf', ['class' => 'col-2', 'type' => 'number', 'label' => 'RF']); ?>
    </div>
    <div class="col-4 px-2">
      <?= $this->Form->control('nm_usuario', ['type' => 'text', 'class' => 'col-8', 'label' => 'Nome']); ?>
    </div>

    <div class="col-2 ps-2">
      <?= $this->Form->control('tp_usuarios_id', ['empty' => ' --- ', 'options' => $tp_usuarios, 'label' => 'Grupo de Usuário']); ?>
    </div>
  </div>

  <div class="col-12 mb-3 d-flex">
    <div class="col-3 pe-2">
      <?= $this->Form->control('cd_cpf', ['class' => 'cpf', 'label' => 'CPF']); ?>
    </div>
    <div class="col-5 ps-2">
      <?= $this->Form->control('email', ['label' => 'E-mail']); ?>
    </div>
  </div>

  <div class="col-12 d-flex">
    <?php if ($pessoa->tp_usuarios_id == 2) { ?>

      <div class="card" style="width: 18rem;">
        <div class="card-header">
          <?= $pessoa->usuario_unid_escolare->setore->nm_setor ?>
        </div>
        <ul class="list-group list-group-flush">
          <?php foreach ($escolas as $setor): ?>
            <li class="list-group-item"><?= $setor->nm_unid_escolar; ?></li>
          <?php endforeach; ?>
        </ul>

      </div>

    <?php } elseif($pessoa->tp_usuarios_id == 1 || $pessoa->tp_usuarios_id == 5) { ?>
      <div class="col-6">
         <span class="text-danger"> <?= !$pessoa->usuario_unid_escolare ? "Usuário não está cadastrado em nenhuma escola" : ""; ?> </span>  
         
        <?= $this->Form->control('pessoa.UsuarioUnidEscolare.unid_escolares_id', ['label' => 'Unidade Escolar', 'options' => $unid_escolares]); ?>
      </div>
    <?php } ?>

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

  <div class="col-4 mt-4">
    <?= $this->Form->control('ic_ativo', ['type' => 'checkbox', 'class' => 'me-3', 'label' => ['text' => 'Inativo']]); ?>

  </div>


  <!-- O botão abaixo está redefinindo a senha 
   ver se não irá enviar e-mail salvando uma nova senha -->
  <div class="row my-4 mx-0 d-flex justify-content-center">
    <div class="col-9">
      <div class="d-flex justify-content-center mt-4">
        <button class="btn btn-warning px-4 me-4" type="button" onclick="history.back()">
          <i class="bi bi-arrow-return-left text-white"></i>
        </button>
        <button class="btn btn-success px-4 ms-4" type="submit"><i class="bi bi-save"></i> </button>
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


  <div class="row d-flex justify-content-center">
    <div class="col-md-6 d-flex justify-content-center my-4">
      <?= $this->Form->postLink(
        '<i class="bi bi-recycle"></i> Redefinir Senha',
        ['controller' => 'Usuarios', 'action' => 'redefinir', $pessoa->id],
        ['escape' => false, 'confirm' => 'Tem certeza, vai redefinir a senha?', 'class' => 'btn btn-sm btn-danger text-white px-4']
      ) ?>

    </div>
  </div>
</div>