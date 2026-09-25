<div class="row col-11 mx-auto">
    <?php
    $c = 1;
    foreach ($titulos as $titulo):
        $qtdEscolas = count($titulo->usuario_unid_escolares);

        $totalPendenciasSupervisor = 0;
        foreach ($titulo->usuario_unid_escolares as $vinculo) {
            if (!empty($vinculo->unid_escolare)) {
                $totalPendenciasSupervisor += $pendenciasPorEscola[$vinculo->unid_escolare->id]->total ?? 0;
            }
        }

        echo
        '<div class="col-12 col-sm-6 col-lg-4 col-xxl-3 g-3 d-flex">
            <div class="w-100 d-flex flex-column">
                <div class="d-flex align-items-center bg-dark-primary rounded-top px-2 shadow">
                    <div class="bg-white rounded-circle d-flex justify-content-center" style="width:1.4rem;height:1.4rem;">' . ($c++) . '</div>
                    <h6 class="mb-0 p-2 text-white text-truncate" style="max-width: 220px;">' . h($titulo->texto) . '</h6>
                </div>
                <div class="bg-white text-dark rounded-bottom p-2 shadow flex-grow-1">
                    <div class="w-100 bg-white py-1 boder-bottom d-flex justify-content-between align-items-center fs-7">
                        <p class="text-dark-primary mb-0">' . $qtdEscolas . ' escola' . ($qtdEscolas != 1 ? 's' : '') . '</p>
                        <div class="rounded-pill alert alert-danger mb-0 px-2 py-1 fs-7">' . $totalPendenciasSupervisor . ' pendências</div>
                    </div>';

        foreach ($titulo->usuario_unid_escolares as $vinculo):
            if (empty($vinculo->unid_escolare)) continue;

            $escolaId = $vinculo->unid_escolare->id;
            $status = $situacaoPorEscola[$escolaId] ?? null;

            if ($status === 'Assinado') {
                $corBolinha = 'success';
                $badgeClasse = 'alert-success';
                $badgeTexto = 'Assinado';
            } elseif ($status === 'Pendente') {
                $corBolinha = 'danger';
                $badgeClasse = 'alert-danger';
                $badgeTexto = 'Pendente';
            } else {
                // Escola sem nenhum relatório finalizado (só rascunho, ou nenhum termo ainda)
                $corBolinha = 'secondary';
                $badgeClasse = 'alert-secondary';
                $badgeTexto = 'Sem termo';
            }

            echo
            $this->Html->link(
                '<div class="d-flex align-items-center justify-content-between py-2 hover rounded border-top border-light-secondary">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-circle-fill text-' . $corBolinha . ' me-2 fs-9"></i>
                        <h6 class="mb-0 text-dark fs-7">' .
                            h($vinculo->unid_escolare->sigla) . ' ' .
                            h($vinculo->unid_escolare->nm_unid_escolar) .
                        '</h6>
                    </div>
                    <div class="rounded-pill text-center fs-7 alert ' . $badgeClasse . ' mb-0 px-2 py-1">' . $badgeTexto . '</div>
                </div>',
                ['action' => 'dashEscolas', $escolaId],
                ['escape' => false]
            );
        endforeach;

        echo
        '</div>
            </div>
        </div>';
    endforeach;
    ?>
</div>