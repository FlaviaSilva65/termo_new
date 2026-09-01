<div class="row col-11 mx-auto">
    <?php

    echo
    '<div class="w-100 d-flex align-items-center pt-3 px-0 justify-content-between">
    <div class="d-flex ms-2">
        <div class="rounded-circle bg-white border-light-secondary me-2 d-flex align-items-center justify-content-center shadow" style="width:3rem;height:3rem;"><i class="bi bi-buildings-fill fs-3 text-dark-primary"></i></div>
        <div class="">
            <h5 class="mb-0 text-dark-primary fw-bold">' . $titulo . '</h5>
            <div class="d-flex justify-content-end">
                <p class="alert alert-primary rounded-pill fs-7 me-2">Total de Escolas: ' . count($titulos) . '</p>
                <p class="alert alert-danger rounded-pill fs-7">Total de Pendencias: '. $totalPendencias . '</p>
            </div>
        </div>
    </div>
</div>';
    foreach ($titulos as $key => $titulo):
        echo
        '<div class="col-12 col-sm-6 col-lg-4 col-xxl-3 g-3 d-flex">
                <div class="w-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center bg-dark-primary rounded-top px-2 shadow">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle d-flex justify-content-center" style="width:1.4rem;height:1.4rem;">' . ($key + 1) . '</div>
                        <h6 class="mb-0 p-2 text-white text-truncate" style="max-width: 220px;">' . $titulo->texto . '</h6>
                        </div>
                        <div class="rounded-pill alert alert-danger fs-7 px-1">' . $titulo->pendencias . ' Pendências</div>
                    </div>
                    <div class="bg-white text-dark rounded-bottom p-2 shadow flex-grow-1">
                        <div class="w-100 bg-white py-1 boder-bottom d-flex justify-content-between fs-7">
                            <p class="text-dark-primary me-3">' . count($titulo->itens) . ' Termos</p>
                            <p class="text-dark-primary"> Criado em: </p>
                            <p class="text-dark-primary me-2"> Situação</p>
                            
                        </div>';
        foreach ($titulo->itens as $item):

            if ($item->situacao == 1) {
                $cor = 'danger';
                $msg = 'Pendente';
            } elseif ($item->situacao == 2) {
                $cor = 'success';
                $msg = 'Assinado';
            } else {
                $cor = 'warning';
                $msg = 'Rascunho';
            }
            echo
            $this->Html->link(
                '<div class="d-flex align-items-center justify-content-between py-2 hover rounded border-top border-light-secondary">
                                     <div class="d-flex align-items-center">
                                     <i class="bi bi-circle-fill text-' . $cor . ' me-2 fs-9"></i>
                                         <h6 class="mb-0 text-dark">' . str_pad($item->termo_id, 3, '0', STR_PAD_LEFT) . '/' . $item->data->format('Y') . '</h6>
                                     </div>' .
                    '<div class="d-flex align-items-center">
                                     <h6 class="mb-0 text-dark">' . $item->data . '</h6>
                                     </div>' .
                    '<div style="width:4rem;" class="alert alert-' . $cor . ' rounded-pill text-center fs-7">' . $msg . '</div>
                                 </div>',
                ['action' => 'manterPerguntas', 1, $titulo->escola_id, $item->id],
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