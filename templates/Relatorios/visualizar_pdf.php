<body style="text-align: justify">
    <div class="d-flex justify-content-end mb-3 d-print-none">
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill shadow">
            <i class="bi bi-printer me-2"></i>Imprimir / Salvar PDF
        </button>
    </div>

    <h4 class="fw-bold text-primary mb-1">
        TERMO DE SUPERVISÃO Nº <?= str_pad($relatorio->termo_id, 3, '0', STR_PAD_LEFT) ?>/<?= $relatorio->data->format('Y') ?>
    </h4>
    <p class="text-secondary mb-0">
        <?= h($escolaName->sigla . ' ' . $escolaName->nm_unid_escolar) ?>
    </p>
    <p class="text-secondary mb-0">
        Data da Visita: <?= h($relatorio->data->format('d/m/Y')) ?>
    </p>
    <?php if ($responsavelNome): ?>
        <p class="text-secondary mb-0">
            Responsável pelo acompanhamento: <?= h($responsavelNome) ?>
        </p>
    <?php endif; ?>
    <p class="text-secondary mb-0">
        Supervisor(a): <?= h($relatorio->usuario->nm_usuario ?? '') ?>
    </p>

    <!-- Cabeçalho do Termo -->
    <div class="text-center mb-4">

    </div>

    <hr>



    <!-- Perguntas agrupadas por dimensão, em ordem -->
    <?php foreach ($perguntasPorDimensao as $dimensaoId => $perguntas): ?>
        <div class="mb-4">
            <h5 class="text-primary fw-bold border-bottom pb-2 mb-3">
                DIMENSÃO <?= h($dimensaoId) ?> —
                <?= mb_strtoupper($dimensoesDb[$dimensaoId]->titCompleto ?? '') ?>
            </h5>

            <?php foreach ($perguntas as $key => $p): ?>
                <?php $respostaSalva = $respostasSalvas[$p->id] ?? null; ?>

                <div class="mb-3 pb-2 border-bottom border-light">
                    <p class="fw-semibold mb-1">
                        <?= ($key + 1) . '. ' . h($p->descricao) ?>
                    </p>

                    <?php if ($p->tipo === 'checkbox'): ?>
                        <div class="ms-3">
                            <?php foreach ($ocorrenciasPorPergunta[$p->id] ?? [] as $ocorrencia): ?>
                                <?php $marcado = in_array($ocorrencia->id, $ocorrenciaIdsSalvas); ?>
                                <span class="badge <?= $marcado ? 'bg-success' : 'bg-light text-secondary border' ?> me-1 mb-1">
                                    <?= $marcado ? '☑' : '☐' ?> <?= h($ocorrencia->nm_tp_ocorrencia) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($p->tipo === 'radio'): ?>
                        <p class="ms-3 mb-0">
                            <strong>Resposta:</strong>
                            <?= h($respostaSalva->resposta ?? '—') ?>
                        </p>

                    <?php elseif ($p->tipo === 'data'): ?>
                        <p class="ms-3 mb-0">
                            <strong>Data:</strong>
                            <?= h($relatorio->data->format('d/m/Y')) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($respostaSalva->observacao)): ?>
                        <p class="ms-3 mb-0 text-secondary fst-italic">
                            Obs: <?= h($respostaSalva->observacao) ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <!-- Assinaturas -->
    <div class="row mt-5 pt-4">
        <div class="col-4 text-center">
            <div class="border-top border-dark pt-1">Supervisor(a)</div>
        </div>
        <div class="col-4 text-center">
            <div class="border-top border-dark pt-1">Direção</div>
        </div>
        <div class="col-4 text-center">
            <div class="border-top border-dark pt-1">Assistência</div>
        </div>
    </div>


</body>