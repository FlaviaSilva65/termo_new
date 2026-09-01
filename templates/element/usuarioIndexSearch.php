<div class="container mb-4">

    <div class="col-12 top-purple-bar mx-0 my-2 d-flex justify-content-center align-items-center">
        <div class="col-3">
            <h5 class="ms-4 text-white">Nome</h5>
        </div>
        <div class="col-2">
            <h5 class="ms-4 text-white">CPF</h5>
        </div>
        <div class="col-2" style="max-width: 240px; white-space: nowrap;overflow:hidden;text-overflow: ellipsis;">
            <h5 class="ms-5 text-white">E-mail</h5>
        </div>
        <div class="col-1">
            <h5 class="ms-3 text-center text-white">Ativo</h5>
        </div>
        <div class="col-1 text-center text-white">
            <h5 class="ms-3">Grupo</h5>
        </div>
        <div class="col-3 text-center">

        </div>
    </div>

    <?php foreach ($usuarios as $user) : ?>
        <div class="col-12 bg-striped mx-0 my-2 d-flex justify-content-center align-items-center">
            <div class="col-3 ms-3" style="font-size: 0.8rem;">
                <?= $user->nm_usuario ?>
            </div>
            <div class="col-2" style="font-size: 0.8rem;">
                <?= $user->cd_cpf ?>
            </div>
            <div class="col-2" style="font-size: 0.8rem; max-width: 240px; white-space: nowrap;overflow:hidden;text-overflow: ellipsis;">
                <?= $user->email ?>
            </div>
            <div class="col-1 text-center">
                <?= ($user->ic_ativo == '1' ? '<i class="bi bi-check text-success fs-2"></i>' : 'Inativo') ?>
            </div>
            <div class="col-1 text-center">
                <?= $user->tp_usuario->nm_tp_usuarios ?>
            </div>
            <div class="col-3 text-center mt-2">
                <?= $this->Html->link('<i class="bi bi-eye"></i>', ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-info text-white px-4', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="bi bi-pencil-square"></i>', ['action' => 'edit', $user->id], ['class' => 'btn btn-sm btn-primary text-white px-4', 'escape' => false]) ?>
                <?= $this->Form->postLink('<i class="bi bi-trash3"></i>', ['action' => 'delete', $user->id], ['confirm' => 'Tem certeza?', 'class' => 'btn btn-sm btn-danger text-white px-4', 'escape' => false]) ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php
    $this->Paginator->setTemplates([
        'number' => '<li class="page-item"><a class="page-link border" href="{{url}}">{{text}}</a></li>',
        'current' => '<li class="page-item active"><span class="page-link border">{{text}}</span></li>',
        'first' => '<li class="page-item"><a class="page-link border" href="{{url}}">{{text}}</a></li>',
        'prev' => '<li class="page-item"><a class="page-link border" href="{{url}}">{{text}}</a></li>',
        'next' => '<li class="page-item"><a class="page-link border" href="{{url}}">{{text}}</a></li>',
        'last' => '<li class="page-item"><a class="page-link border" href="{{url}}">{{text}}</a></li>',
    ]);
    ?>

    <?= $this->Paginator->counter(
        'Página {{page}} de {{pages}} , exibindo {{current}} registros de um total de {{count}}.'
    ); ?>
    <nav>
        <ul class="pagination">
            <?= $this->Paginator->first('<i class="bi bi-chevron-double-left"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->prev(
                '<i class="bi bi-chevron-left"></i>',
                [
                    'escape' => false,
                    'templates' => [
                        'prevActive' => '<li class="page-item"><a class="page-link border" rel="prev" href="{{url}}">{{text}}</a></li>',
                        'prevDisabled' => '<li class="page-item disabled"><span class="page-link border">{{text}}</span></li>',
                    ]
                ]
            ) ?>

            <?= $this->Paginator->numbers() ?>

            <?= $this->Paginator->next(
                '<i class="bi bi-chevron-right"></i>',
                [
                    'escape' => false,
                    'templates' => [
                        'nextActive' => '<li class="page-item"><a class="page-link border" rel="next" href="{{url}}">{{text}}</a></li>',
                        'nextDisabled' => '<li class="page-item disabled"><span class="page-link border">{{text}}</span></li>',
                    ]
                ]
            ) ?>

            <?= $this->Paginator->last('<i class="bi bi-chevron-double-right"></i>', ['escape' => false,]) ?>

        </ul>

    </nav>
</div>