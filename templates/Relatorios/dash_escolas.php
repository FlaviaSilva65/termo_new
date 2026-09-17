<div class="row col-11 mx-auto px-3">

    <div class="bg-white shadow rounded border-secondary mt-4 pt-2">
        <?php
        // dd($identity->tp_usuarios_id);
        
        foreach ($relatorios as $i => $relatorio) : ?>
            <?php
            if ($relatorio->usuario->tp_usuarios_id == 2 && $relatorio->situacao == 2) {
                $situacao = 2;
            } else {
                $situacao = $relatorio->situacao;
            }
            switch ($situacao) {
                case 1:
                    $classeSituacao = 'alert-warning';
                    $textoSituacao = 'Rascunho';
                    $acao = 'Editar';
                    $icone = '<i class="bi bi-pencil-square"></i>';
                    break;

                case 2:
                    $classeSituacao = 'alert-success';
                    $textoSituacao = 'Assinado';
                    $acao = 'Visualizar';
                    $icone = '<i class="bi bi-eye-fill"></i>';
                    break;

                case 3:
                    $classeSituacao = 'alert-danger';
                    $textoSituacao = 'Pendente';
                    $acao = $relatorio->usuario->tp_usuarios_id == 2 ? 'Visualizar' : 'Assinar';
                    $icone = $relatorio->usuario->tp_usuarios_id == 2 ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-pen-fill"></i>';
                    break;

                default:
                    $classeSituacao = 'alert-secondary';
                    $textoSituacao = 'Não informado';
                    $acao = 'Visualizar';
                    $icone = '<i class="bi bi-exclamation"></i>';
            }
            ?>

            <div class="row border-bottom border-light-secondary rounded px-2 pt-2 pb-1 g-0 hover">
                <div class="col-12 col-md-4 col-lg">
                    <p class="text-secondary lh-1 fs-7">Termo</p>
                    <p class="text-dark">TVS-<?= sprintf('%03d', $relatorio->termo_id) ?>/<?= $relatorio->data->format('Y') ?></p>
                </div>
                <div class="col-4 col-lg">
                    <p class="text-secondary lh-1 fs-7">Data da Visita</p>
                    <p class="text-dark"><?= $relatorio->data ?></p>
                </div>
                <div class="col">
                    <p class="text-secondary lh-1 fs-7">Supervisor(a)</p>
                    <p class="text-dark"><?= h($relatorio->usuario->nm_usuario) ?></p>
                </div>

                <div class="col-12 d-flex col-lg-4">
                    <div class="col-1">
                        <p class="text-secondary lh-1 fs-7">Situação</p>
                        <div style="width:6rem; margin-top: 0.2rem" class="rounded-pill fs-7 text-center alert  <?= h($classeSituacao) ?>">
                            <?= h($textoSituacao) ?>
                        </div>
                    </div>
                    <div class="col text-end">
                        <p class="text-secondary lh-1 fs-7"> </p>
                        <?= $this->Html->link(
                            '<span class="icone-circulo">' . $icone . '</span>' . $acao,
                            ['action' => 'manter_perguntas', 1, $escola->id, $relatorio->id],
                            [
                                'class' => 'btn btn-primary btn-sm btn-s-pill shadow btn-visualizar-assinar me-2',
                                'style' => 'width: 165px; justify-content: center;',
                                'escape' => false
                            ]
                        ) ?>

                    </div>
                </div>

            </div>
        <?php
        endforeach;
        ?>
    </div>
    <div class="w-100 text-end mt-3">
        <?= $this->Html->link(
            '<span class="icone-circulo"><i class="bi bi-arrow-left"></i></span>Voltar',
            'javascript:history.back()',
            [
                'class' => 'btn btn-primary btn-sm btn-s-pill shadow btn-visualizar-assinar me-3',
                'style' => 'width: 165px; justify-content: center;',
                'escape' => false
            ]
        ) ?>
    </div>
</div>
<style>
    .btn-visualizar-assinar {
        position: relative;
        display: inline-flex;
        align-items: center;
        /* background-color: #16375a; */
        /* color: #fff; */
        border-radius: 50px;
        padding: 0.4rem 1.2rem 0.4rem 2.6rem;
        font-size: 0.85rem;
        font-weight: 500;
        border: none;
        text-decoration: none;
        /* transition: background-color 0.2s ease; */
    }


    .btn-visualizar-assinar .icone-circulo {
        position: absolute;
        left: 3px;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        /* background-color: #3f7cb0; */
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>