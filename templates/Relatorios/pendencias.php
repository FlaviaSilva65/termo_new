<div class="row col-11 mx-auto">

    <div class="bg-white rounded shadow mt-3 py-3">
        <?php foreach ($pendenciasPorRelatorio as $relatorioId => $itens): ?>
            <?php
            $relatorio = $relatorios[$relatorioId] ?? null;
            $termoFormatado = $relatorio
                ? 'TVS-' . $relatorio->data->year . '/' . str_pad($relatorio->termo_id, 3, '0', STR_PAD_LEFT)
                : "Relatório #{$relatorioId}";
            $dataVisita = $relatorio ? date('d/m/Y', strtotime($relatorio->data)) : '-';
            $supervisor = $relatorio->usuario->nm_usuario ?? '-';
            ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                <div>
                    <p class="mb-0 fs-8 text-secondary">Termo</p>
                    <p class="mb-0 fw-semibold text-primary"><?= h($termoFormatado) ?></p>
                </div>
                <div>
                    <p class="mb-0 fs-8 text-secondary">Data da Visita</p>
                    <p class="mb-0 fw-semibold"><?= $relatorio->data ?></p>
                </div>
                <div>
                    <p class="mb-0 fs-8 text-secondary">Supervisor(a)</p>
                    <p class="mb-0 fw-semibold"><?= h($supervisor) ?></p>
                </div>
                <div>
                    <p class="mb-0 fs-8 text-secondary">Situação</p>
                    <span class="badge rounded-pill bg-danger-subtle text-danger">
                        <?= count($itens) ?> Pendência<?= count($itens) > 1 ? 's' : '' ?>
                    </span>
                </div>
                <?php /* $this->Html->link(
                    '<i class="bi bi-eye-fill me-1"></i>Ver Pendências',
                    [
                        'action' => 'manterPerguntas',
                        $menorDimensaoPorRelatorio[$relatorioId] ?? 1,
                        $escola_id,
                        $relatorioId,
                        '?' => ['pendencias' => 1],
                    ],
                    [
                        'class' => 'btn btn-primary btn-sm btn-s-pill shadow',
                        'escape' => false,
                    ]
                ) */ ?>
                <?= $this->element('btn_pill', [
                    'label' => 'Ver Pendências',
                    'cor' => 'primary',
                    'icon'  => 'eye-fill',
                    'url'   => [
                        'action' => 'manterPerguntas',
                        $menorDimensaoPorRelatorio[$relatorioId] ?? 1,
                        $escola_id,
                        $relatorioId,
                        '?' => ['pendencias' => 1],
                    ],
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="w-100 text-end mt-3">
        <?php /* $this->Html->link(
            '<span class="icone-circulo"><i class="bi bi-arrow-left"></i></span>Voltar',
            ['action' => 'dash-supervisor', $identity->id],
            ['class' => 'btn btn-primary btn-sm btn-s-pill shadow btn-visualizar-assinar pe-5 me-2',
            'style' => 'width: 155px; justify-content: center;',
             'escape' => false]
        ) */ ?>
        <?= $this->element('btn_pill', [
            'label' => 'Voltar',
            'cor' => 'success',
            'icon'  => 'arrow-left',
            'url'   => ['action' => 'dash-supervisor', $identity->id],
            'class' => 'btn-pill--fixed',
        ]) ?>
    </div>
</div>