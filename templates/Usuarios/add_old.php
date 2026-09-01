<div class="container">

  <?= $this->Form->create($usuario, ['accept' => '.jpg', 'enctype' => 'multipart/form-data', 'novalidate']) ?>

  <div class="row mt-5 mb-2 mx-0 d-flex justify-content-center">
    <div class="col-2">
      <?= $this->Form->control('cd_rf', ['label' => 'RF']); ?>
    </div>
    <div class="col-6">
      <?= $this->Form->control('nm_usuario', ['label' => 'Nome']); ?>
    </div>
  </div>
  <div class="row my-2 mx-0 d-flex justify-content-center">
    <div class="col-8">
      <?= $this->Form->control('email', ['label' => 'E-mail Institucional']); ?>
    </div>
  </div>
  <div class="row my-2 mx-0 d-flex justify-content-center">
    <div class="col-2">
      <?= $this->Form->control('username', ['label' => 'Usuário']); ?>
    </div>
    <div class="col-3">
      <?= $this->Form->control('password', ['label' => 'Senha']); ?>
    </div>
    <div class="col-3">
      <?= $this->Form->control('tp_usuarios_id', ['label' => 'Grupo de Usuário', 'empty' => '--- ', 'options' => $tp_usuarios]); ?>
    </div>
  </div>


  <div class="row mt-5 mb-2 mx-0 d-flex justify-content-center">
    <div class="col-3">
      <input type="text" class="form-control border border-secondary rounded-start" id="nomeList" placeholder="Digite o nome da Escola" aria-label="Input group example" aria-describedby="btnGroupAddon">
    </div>
    <div class="col-5" id="tableAjax">

    </div>
  </div>


  <div class="row my-2 mx-0 d-flex justify-content-center">

    <div class="col-4">

      <?= $this->Form->control('Anexar Assinatura', ['class' => 'my-0 py-0', 'type' => 'file', 'name' => 'arquivo', 'label' => ['class' => 'btn btn-secondary'], 'style' => 'opacity: 0;', 'id' => 'formFile']); ?>

    </div>


    <div class="col-4 mt-4">
      <?= $this->Form->control('ic_ativo', ['type' => 'checkbox', 'class' => 'me-3', 'label' => ['text' => 'Inativo']]); ?>

    </div>
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
  <?= $this->Form->end() ?>
</div>
<script>
  // function ver_tp_usuario(el) {
  //   var tipo = el.value;
  //   // alert(tipo);
  // }

  function ver_escola() {
    var escola = document.getElementById('nomeList').value;

    // alert(escola);

    if (escola.length === 0) {
      alert('Digite a escola');

      document.getElementById('nomeList').focus();

      return false;

    }
    // else {
    //   fileInput.submit()
    // }
  }

  function lista_unid_escolar(el) {
    var data = el.value;
    // alert(data);
    // buscaUnids(unid);
    $.ajax({
      method: 'get',
      url: "<?php echo $this->Url->build(['controller' => 'Usuarios', 'action' => 'buscaunids']); ?>",
      data: {
        keyword: data,
      },
      success: function(response) {
        console.log(response);
      },
      error: function(response) {
        alert('b')
      },
      complete: function(response) {
        $('#tableAjax').html(response.responseText);
      }
    })
  }

  $('document').ready(function() {
    $('#nomeList').keyup(function() {
      var searchkey = $(this).val()
      searchTags(searchkey);
    });

    function searchTags(keyword) {
      var data = keyword;
      if (!data) data = 'A';
      $.ajax({
        method: 'get',
        url: "<?php echo $this->Url->build(['controller' => 'Usuarios', 'action' => 'buscaunids']); ?>",
        data: {
          keyword: data,

        },
        success: function(response) {
          // alert('a')
          console.log(response);
        },
        error: function(response) {
          alert('b')

        },
        complete: function(response) {
          $('#tableAjax').html(response.responseText);
        }
      })
    }
  });
</script>