<div class="row col-11 mx-auto">
    <div class="w-100 d-flex align-items-center pt-3 px-0 justify-content-between">
        <div class="d-flex ms-2">
            <div class="rounded-circle bg-white border-light-secondary me-2 d-flex align-items-center justify-content-center shadow" style="width:3rem;height:3rem;"><i class="bi bi-buildings-fill fs-3 text-dark-primary"></i></div>
            <div class="">
                <h5 class="mb-0 text-dark-primary fw-bold"><?= h($escolaName->nm_unid_escolar ?? '') ?></h5>
                <div class="d-flex gap-2 mt-1">
                    <span class="badge rounded-pill bg-primary-subtle text-primary">
                        Total de Termos: <?= count($pendenciasPorRelatorio) ?>
                    </span>
                    <span class="badge rounded-pill bg-danger-subtle text-danger">
                        Pendências em aberto: <?= array_sum(array_map('count', $pendenciasPorRelatorio)) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow mt-3 py-3">
        <?php foreach ($pendenciasPorRelatorio as $relatorioId => $itens): ?>
            <?php
            $relatorio = $relatorios[$relatorioId] ?? null;
            $termoFormatado = $relatorio
                ? 'TVS-' . date('Y', strtotime($relatorio->data)) . '/' . str_pad($relatorio->termo_id, 3, '0', STR_PAD_LEFT)
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
                    <p class="mb-0 fw-semibold"><?= h($dataVisita) ?></p>
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
                <?= $this->Html->link(
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
                ) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="w-100 text-end mt-3">
        <?= $this->Html->link(
            '<i class="bi bi-arrow-left me-4"></i>Voltar',
            ['action' => 'dash-supervisor', $identity->id],
            ['class' => 'btn btn-primary btn-sm btn-s-pill shadow pe-5 me-2', 'escape' => false]
        ) ?>
    </div>
</div>
</div>