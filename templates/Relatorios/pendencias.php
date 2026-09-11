<div class="col-12 col-lg-11 d-flex mx-auto">
    <div class="w-100">
        <div class="w-100 d-flex bg-white p-3 pb-2 rounded-top shadow align-items-center">
            <i class="bi bi-building fs-2 bg-primary text-white rounded me-2 pt-1 px-1 lh-1" style="height:1.3em;"></i>
            <div>
                <p class="mb-0 fs-5 fw-bold text-primary">
                    <?= h($escolaName->nm_unid_escolar ?? '') ?>
                </p>
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

        <div class="bg-white rounded-bottom shadow">
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
                        '<i class="bi bi-eye me-1"></i>Ver Pendências',
                        [
                            'action' => 'manterPerguntas',
                            $itens[0]['dimensao'],
                            $escola_id,
                            $relatorioId,
                            '?' => ['pendencias' => 1],
                        ],
                        [
                            'class' => 'btn btn-primary btn-sm rounded-pill shadow-sm',
                            'escape' => false,
                        ]
                    ) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left me-2"></i>Voltar',
                ['action' => 'dash-supervisor'],
                ['class' => 'btn btn-primary btn-sm rounded-pill shadow', 'escape' => false]
            ) ?>
        </div>
    </div>
</div>