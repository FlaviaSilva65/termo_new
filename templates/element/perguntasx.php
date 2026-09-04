<style>
    .pads {
        padding-left: 2.3rem;
    }
</style>
<?php
echo
'<div class="w-100 d-flex bg-white p-3 pb-2 rounded-top shadow">
        <i class="bi bi-journal-text fs-2 bg-primary text-white rounded me-2 pt-1 px-1 lh-1"></i>
        <div class="">
            <p class="mb-0 fs-6 mb-0 fw-bold text-primary">
                DIMENSÃO ' . $romanos[$dimensao] . ' - ' . mb_strtoupper($dimensoes[$dimensao - 1]->titCompleto) .
    '</p>
            <p class="mb-0 fs-7 text-secondary">' .
    $dimensoes[$dimensao - 1]->pergTotal . ' Perguntas
            </p>
        </div>
    </div>' .
    $this->Form->create($relatorio, ["id" => "form-perguntas", "autocomplete" => "off", "novalidate"]);
foreach ($perguntas as $key => $p):
    $respostaSalva = $respostasSalvas[$p->id] ?? null;

    echo
    '<div class="d-flex xp-2 shadow align-items-top">
                <div class="rounded-circle bg-primary mt-2 ms-2 me-2 d-flex align-items-center justify-content-center" style="width:1.5rem;height:1.3rem;">
                    <h6 class="mb-0 text-white">' . ($key + 1) . '</h6>
                </div>
                <div class="w-100">
                    <p class="mb-1 text-dark small">' . $p->descricao . '</p>';
    if ($p->tipo == "checkbox") {
        //se checkbox
        echo
        '<div class="row g-1">';
        foreach ($ocorrencias[$p->id] as $key => $ocorrencia) {
            $marcado = in_array($ocorrencia->id, $ocorrenciaIdsSalvas);
            echo
            '<div class="col-4 col-lg-3 d-flex align-items-stretch">' .
                $this->Form->control("respostas.{$p->id}.ocorrencias.{$ocorrencia->id}", [
                    'type' => 'checkbox',
                    'checked' => $marcado,
                    'label' => [
                        'text' => '<span>' . $ocorrencia->nm_tp_ocorrencia . '</span>',
                        'class' => 'checkbox-resposta mt-0',
                        'escape' => false,
                    ],
                    'class' => ''
                ]) .
                '</div>';
        }
        echo
        '</div>';
    } else if ($p->tipo == "radio") {
        // se radio button
        echo
        '<div class="grupo-resposta">' .
            $this->Form->control("respostas.{$p->id}.resposta", [
                'type' => 'radio',
                'options' => json_decode($p->opcoes ?? '', true),
                'value' => $respostaSalva->resposta ?? null,
                'label' => false,
                'legend' => false,
                'class' => 'form-check-input mt-0',
                'templates' => [
                    'radioWrapper' => '<div class="resposta-radio">{{label}}</div>',
                    'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
                    'radioLabel' => '<label{{attrs}}>{{input}}{{text}}</label>',
                ],
            ]) .
            '</div>';
    } else if ($p->tipo == "data") {
        echo '<div class="pads">' .
            $this->Form->control('relatorio.data', [
                'type' => 'date',
                'label' => false,
                'class' => 'mb-2 py-1 text-secondary',
                'style' => 'width:9rem;',
                'value' => $relatorio->data ?? null,
            ]) .
            '</div>';
    }
    echo
    '<div>' .
        $this->Form->control("respostas.{$p->id}.observacao", [
            "type" => "textarea",
            "rows" => 1,
            "label" => false,
            "class" => "mt-2 smart-textarea place-cor",
            "placeholder" => "+ Adicionar observação",
            "value" => $respostaSalva->observacao ?? '',
        ]) .
        '</div>
                </div>
                <div class="border-start ms-2 px-2 d-flex flex-wrap align-items-center d-print-none">
                    <div>
                        <div class="d-flex mb-2 justify-content-center">
                            <i class="bi bi-check-circle me-2 fs-6 text-success"></i>
                            <div>
                                <p class="d-flex mb-0 fs-7 text-success lh-1">Respondida</p>
                                <p class="text-nowrap mb-0 fs-8 text-secondary">em ' . $respostaSalva?->modified . '</p>' .
        '</div>
                        </div>';
    // $ra = rand(1, 0);

    // echo
    // $this->Html->link(
    //     '<div class="d-flex align-items-center">
    //         <i class="bi bi-'.($ra ? "check" : "exclamation").'-circle me-1 text-white fs-6 py-2 lh-1"></i>
    //         <span class="lh-sm">'.($ra ? "Requerido" : "Requer").'<br>Acompanhamento</span>
    //     </div>',
    //     ['action'=>'acompanhamento', $p->id],
    //     ['class'=>'btn btn-'.($ra ? 'success' : 'warning').' btn-sm btn-s-pill shadow fs-8 py-0', 'escape'=>false]
    // );
    $statusSalvo = $respostaSalva->status ?? 0; // default: 0 = requer acompanhamento

    echo
    '<button type="button" class="btn-acompanhamento btn btn-' . ($statusSalvo ? 'success' : 'warning') . ' btn-sm btn-s-pill shadow fs-8 py-0" data-pergunta-id="' . $p->id . '">
    <div class="d-flex align-items-center">
        <i class="bi bi-' . ($statusSalvo ? "check" : "exclamation") . '-circle me-1 text-white fs-6 py-2 lh-1"></i>
        <span class="lh-sm">' . ($statusSalvo ? "Requerido" : "Requer") . '<br>Acompanhamento</span>
    </div>
     </button>' .
        '</div>
                </div>
            </div>';
    echo $this->Form->hidden("respostas.{$p->id}.status", ['value' => $statusSalvo]);
endforeach;
echo
$this->Form->end();
