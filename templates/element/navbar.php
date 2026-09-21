<?php
// Essa variável será excluída 
$modelo = 1;

$acaoAtual = $this->request->getParam('action');

$urlInicio = [
    'plugin' => null,
    'controller' => 'Relatorios'
];



if (
    isset($identity) && $identity->tp_usuarios_id == 2 && $this->request->getParam('action') == 'dashEscolas'
    || ($this->request->getParam('action') == 'providencia')
) {
    $urlPend = [
        'plugin' => null,
        'controller' => 'Relatorios',
        'action' => 'dashEscolas',
        $escola->id,
        '?' => [
            'pendentes' => 1
        ]
    ];
}

if (isset($identity) && $identity->tp_usuarios_id == 2) {

    $urlInicio['action'] = 'dashSupervisor';
    $urlInicio[] = $identity->id;
} elseif (isset($identity) && $identity->tp_usuarios_id == 4) {

    $urlInicio['action'] = 'dashSubsecretaria';
} elseif (isset($identity) && ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5)) {

    $urlInicio['action'] = 'dashDiretorEscolas';
    $urlInicio[] = $identity->id;
} elseif (isset($identity) && ($identity->tp_usuarios_id == 6 || $identity->tp_usuarios_id == 9)) {
    $urlInicio = [
        'controller' => 'Usuarios',
        'action' => 'index'
    ];
}
?>

<!-- Calculando os relatorios em assinatura -->


<div class="w-100 bg-primary text-white px-3">
    <div class="col-11 mx-auto d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <?php

            if (isset($identity)) {

                echo
                $this->Html->link(
                    '<i class="bi bi-house fs-4 me-2"></i><span class="fs-6">Início</span>',
                    $urlInicio,
                    ['class' => 'd-flex align-items-center py-0 me-4 px-1 btn btn-primary btn-sm', 'escape' => false]
                );
            } ?>
            <?php if ($this->request->getParam('action') === 'dashEscolas' && isset($escola)): ?>
                <?php
                $urlNovoRelatorio = [
                    'plugin' => null,
                    'controller' => 'Relatorios',
                    'action' => 'manterPerguntas',
                    1,
                    $escola->id,
                    '?' => ['novo' => 1],
                ];
                ?>

                <!-- NOVO TERMO / RELATÓRIO -->
                <?= $this->Html->link(
                    '<i class="bi bi-file-earmark-plus fs-4 me-2"></i>
                        <span class="fs-6">Novo Termo</span>',
                    $urlNovoRelatorio,
                    [
                        'class' => 'd-flex align-items-center py-0 me-3 px-1 btn btn-primary btn-sm',
                        'escape' => false
                    ]
                ) ?>

            <?php endif; ?>

            <?php if (($acaoAtual === 'manterPerguntas'  && isset($relatorio)) || $acaoAtual === 'dashEscolas'): ?>
                <?php
                $termo = $relatorio->termo_id ?? null;
                // Situação
                $emPreenchimento = isset($relatorio) ? $relatorio->ic_rascunho == 1 : false;

                // Escola
                if (isset($escolaName)) {
                    $nomeEscola = $escolaName->sigla . ' ' . $escolaName->nm_unid_escolar;
                } elseif (isset($escola)) {
                    $nomeEscola = $escola->sigla . ' ' . $escola->nm_unid_escolar;
                } else {
                    $nomeEscola = '';
                }
                ?>

                <!-- TERMO -->
                <?php if ($termo): ?>
                    <div class="d-flex align-items-center me-4">
                        <i class="bi bi-journal-text fs-4 me-2"></i>

                        <h6 class="mb-0">
                            Termo de Supervisão - Nº <?= sprintf('%03d', $termo) ?>
                        </h6>
                    </div>
                <?php endif; ?>

                <!-- ESCOLA -->
                <?php if ($nomeEscola): ?>

                    <div class="d-flex align-items-center me-4">
                        <i class="bi bi-buildings me-2 fs-4"></i>

                        <h6 class="mb-0">
                            <?= h($nomeEscola) ?>
                        </h6>
                    </div>

                <?php endif; ?>
                <!-- PENDÊNCIAS (só na tela dashEscolas, logo após o nome da escola) -->
                <?php if ($this->request->getParam('action') === 'dashEscolas' && isset($escola)): ?>

                    <!-- Colocar uma URL para filtrar somente os termos Pendentes de Assinatura -->
                    <?= $this->Html->link(
                        '<h6 class="info-rounded-pill align-self-center bg-dark-warning mb-0 d-flex align-items-center me-3">
                        <span class="info-s-pill me-1">
                            <i class="bi bi-pencil-square"></i>
                        </span>
                        Sem assinatura: ' . (isset($relatoriosPendentes) ? $relatoriosPendentes : 0) . '</h6>',
                        $urlPend,
                        [
                            'class' => 'd-flex align-items-center py-0 px-1 btn btn-primary btn-sm',
                            'escape' => false,
                            'title' => 'Termos pendentes de assinatura'
                        ]
                    ) ?>
                <?php endif; ?>

                <!-- PENDÊNCIAS (Aguardando Providências -->
                <?php if ($this->request->getParam('action') === 'dashEscolas' && isset($pendencias)): ?>
                    <?php
                    $urlPendencias = [
                        'plugin' => null,
                        'controller' => 'Relatorios',
                        'action' => 'pendencias',
                        $escola->id
                    ];
                    ?>

                    <?= $this->Html->link(
                        '<h6 class="info-rounded-pill align-self-center bg-danger mb-0 d-flex align-items-center" style="letter-spacing: 0.10rem;">
                        <span class="info-s-pill me-1">
                            <i class="bi bi-check-square"></i>
                        </span>
                        Pendências: ' . count($pendencias) . '</h6>',
                        $urlPendencias,
                        [
                            'class' => 'd-flex align-items-center py-0 px-1 btn btn-primary btn-sm',
                            'escape' => false,
                            'title' => 'Termos com pendências apontadas'
                        ]
                    ) ?>

                <?php endif; ?>

                <!-- SITUAÇÃO -->
                <?php if (isset($relatorio)): ?>
                    <h6 class="info-rounded-pill align-self-center
                        <?= $emPreenchimento ? 'bg-warning text-dark' : 'bg-dark-quaternary' ?>
                        small mb-0 d-flex align-items-center">

                        <?php if ($emPreenchimento): ?>

                            <span class="info-s-pill me-1">
                                <i class="bi bi-pencil-square"></i>
                            </span>
                            Em preenchimento
                        <?php else: ?>
                            <span class="info-s-pill me-1">
                                <i class="bi bi-check-circle"></i>
                            </span>
                            Finalizado
                        <?php endif; ?>
                    </h6>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (
                isset($identity) &&
                ($identity->tp_usuarios_id == 6 || $identity->tp_usuarios_id == 9)
            ): ?>
                <?php
                $urlUsuarios = [
                    'controller' => 'Usuarios',
                    'action' => 'add'
                ];
                ?>

                <div class="dropdown pushable-ignore order-2">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm d-flex align-items-center py-0 px-1"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        data-bs-auto-close="outside">
                        <i class="bi bi-person-workspace fs-4 me-2"></i>

                        <span class="fs-6 d-none d-md-inline">
                            Usuários
                        </span>

                        <i class="bi bi-chevron-down smaller ms-1"></i>
                    </button>


                    <div class="dropdown-menu">

                        <?= $this->Html->link(
                            '<i class="bi bi-people me-2"></i>
                                <span>Cadastrar</span>',
                            $urlUsuarios,
                            [
                                'class' => 'dropdown-item d-flex align-items-center',
                                'escape' => false
                            ]
                        ) ?>

                    </div>

                </div>

            <?php endif; ?>
        </div>

        <?php if (isset($identity)) {  ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-person-circle fs-4 text-white me-2"></i>
                    <div class="text-start">
                        <p class="fs-7 lh-1 mb-0">' . h($funcao->nm_tp_usuarios) . '(a)</p>
                        <h6 class="small mb-0 lh-1 fs-6">' . h($identity->nm_usuario) . '</h6>
                    </div>',
                ['controller' => 'Usuarios', 'action' => 'logout'],
                ['class' => 'd-flex align-items-center btn btn-primary btn-sm py-0', 'escape' => false, 'title' => 'Sair']
            );
            ?>
        <?php } ?>

    </div>
</div>