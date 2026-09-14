<?php
$url = [
    'plugin' => null,
    'controller' => 'Relatorios'
];

// Essa variável será excluída 
$modelo = 1;

if (isset($identity) && $identity->tp_usuarios_id == 2 && $this->request->getParam('action') == 'dashEscolas' || ($this->request->getParam('action') == 'providencia')) {
    $urlPend = [
        'plugin' => null,
        // 'controller' => 'Providencias',
        'action' => 'providencia'
    ];
    $urlPend[] = $escola->id;
}

if (isset($identity) && $identity->tp_usuarios_id == 2) {

    $url['action'] = 'dashSupervisor';
    $url[] = ($setor->setores_id ? $setor->setores_id : $identity->id);
} elseif (isset($identity) && $identity->tp_usuarios_id == 4) {

    $url['action'] = 'dashSubsecretaria';
} elseif (isset($identity) && ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5)) {

    $url['action'] = 'dashDiretorEscolas';
    $url[] = $identity->id;

    $urlPend = [
        'plugin' => null,
        // 'controller' => 'Providencias',
        'action' => 'providencia'
    ];
    $urlPend[] = $escola->id;
} elseif (isset($identity) && ($identity->tp_usuarios_id == 6 || $identity->tp_usuarios_id == 9)) {
    $url = [
        'controller' => 'Usuarios',
        'action' => 'index'
    ];
}
?>
<div class="w-100 bg-primary text-white px-3">
    <div class="col-11 mx-auto d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <?php
            if (isset($relatorio) && $relatorio->ic_rascunho == 1) {
                $opc = true;
                $relatorio = $relatorio->termo_id;
            } else {
                $opc = false;
                $relatorio = isset($relatorio) ? $relatorio->termo_id : '';
            }
            if (isset($escolaName)) {
                $unidade = $escolaName->sigla . ' ' . $escolaName->nm_unid_escolar;
            }

            if (isset($identity)) {

                // pr($url);

                echo
                $this->Html->link(
                    '<i class="bi bi-house fs-4 me-2"></i><span class="fs-6">Início</span>',
                    $url,
                    ['class' => 'd-flex align-items-center py-0 me-4 px-1 btn btn-primary btn-sm', 'escape' => false]
                ) .

                    (
                        ($modelo == 1 && $this->request->getParam('action') == 'manterPerguntas')
                        ?   '<i class="bi bi-journal-text fs-4 text-white me-2"></i>
                        <h6 class="mb-0 me-4">Termo de Supervisão - Nº ' . $relatorio . '</h6>
                        <i class="bi bi-buildings me-2 fs-4"></i>
                        <h6 class="mb-0 me-4">' . $unidade . '</h6>
                        <h6 class="info-rounded-pill align-self-center ' . ($opc ? 'bg-warning text-dark' : 'bg-dark-quaternary') . ' small mb-0 d-flex align-items-center">' .
                        (
                            $opc
                            ? '<span class="info-s-pill me-1"><i class="bi bi-pencil-square"></i></span>Em preenchimento'
                            : '<span class="info-s-pill me-1"><i class="bi bi-check-circle"></i></span>Assinado em ' . date('d/m/Y')
                        ) .
                        '</h6>'
                        : (
                            $modelo == 'a'
                            ? ' '
                            : ''
                        )
                    );
            } ?>

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
            <?php // } else { 
            ?>
            <?php //$this->Form->postLink(
            //     '<i class="bi bi-circle fs-4 me-2"></i><span class="fs-6">Logar</span>',
            //     ['controller' => 'Usuarios', 'action' => 'login'],
            //     ['class' => 'd-flex align-items-center btn btn-primary btn-sm py-0', 'escape' => false, 'title' => 'Entrar']
            // );
            ?>

        <?php } ?>

    </div>
</div>