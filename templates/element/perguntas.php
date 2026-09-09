<style>
    .pads {
        padding-left: 2.3rem;
    }
</style>

<div class="w-100 d-flex bg-white p-3 pb-2 rounded-top shadow">
    <i class="bi bi-journal-text fs-2 bg-primary text-white rounded me-2 pt-1 px-1 lh-1" style="height:1.3em;"></i>

    <div>
        <p class="mb-0 fs-6 fw-bold text-primary">
            DIMENSÃO <?= $romanos[$dimensao] ?> -
            <?= mb_strtoupper($dimensoes[$dimensao - 1]->titCompleto) ?>
        </p>

        <p class="mb-0 fs-7 text-secondary">
            <?= $dimensoes[$dimensao - 1]->pergTotal ?> Perguntas
        </p>
    </div>
</div>

<?= $this->Form->create($relatorio, [
    "id" => "form-perguntas",
    "autocomplete" => "off",
    "novalidate",
    "url" => array_merge(
        ['action' => 'manterPerguntas', $dimensao, $escola_id, $relatorio->id],
        $somentePendencias ? ['?' => ['pendencias' => 1]] : []
    ),
]) ?>

<?php foreach ($perguntas as $key => $p): ?>

    <?php
    $respostaSalva = $respostasSalvas[$p->id] ?? null;
    ?>
    <div class="d-flex xbg-white shadow border-5 border-start importanciaCor<?= substr($p->importancia, 0, 1) ?>">
        <div class="w-100">
            <div class="d-flex align-items-center my-1">
                <div
                    class="rounded-circle bg-primary ms-2 me-2 d-flex flex-shrink-0 align-items-center justify-content-center"
                    style="width:1.3rem;height:1.3rem;">
                    <h6 class="mb-0 text-white">
                        <?= $key + 1 ?>
                    </h6>
                </div>
                <p class="text-dark small">
                    <?= $p->descricao ?>
                </p>
            </div>
            <?php if ($p->tipo == "checkbox"): ?>

                <!-- Checkbox -->
                <div class="row g-1 pads">
                    <?php foreach ($ocorrencias[$p->id] as $key => $ocorrencia): ?>
                        <?php
                        $marcado = in_array(
                            $ocorrencia->id,
                            $ocorrenciaIdsSalvas
                        );
                        ?>
                        <div class="col-4 col-lg-3 d-flex align-items-stretch">
                            <?= $this->Form->control(
                                "respostas.{$p->id}.ocorrencias.{$ocorrencia->id}",
                                [
                                    'type' => 'checkbox',
                                    'checked' => $marcado,
                                    'label' => [
                                        'text' => '<span>' . $ocorrencia->nm_tp_ocorrencia . '</span>',
                                        'class' => 'checkbox-resposta mt-0',
                                        'escape' => false,
                                    ],
                                ]
                            ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($p->tipo == "radio"): ?>
                <!-- Radio button -->
                <div class="d-flex flex-wrap align-items-center gap-3 pads">

                    <div class="grupo-resposta">
                        <?= $this->Form->control(
                            "respostas.{$p->id}.resposta",
                            [
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
                            ]
                        ) ?>
                    </div>

                    <?php if ($p->id == 35): ?>
                        <input type="hidden" name="id_usuario" value="">
                        <div class="usuario-select">
                            <div class="usuario-select-wrapper">
                                <div class="usuario-select-display">
                                    <span class="usuario-selecionado">
                                        ⇩ Selecione quem acompanhou
                                    </span>
                                </div>
                                <div class="usuario-select-options p-1">
                                    <?php foreach ($usuarios_lista as $value => $nome) ?>
                                    <div class="usuario-option px-1 rounded text-secondary" data-value="<?= $value ?>">
                                        <i class="bi bi-person-circle pt-3"></i>
                                        <span class="usuario-nome text-nowrap"><?= h($nome) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?= $this->Form->error('id_usuario') ?>

                        </div>

                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <?php if ($p->tipo == "data"): ?>
                <div class="pads">
                    <?= $this->Form->control(
                        'relatorio.data',
                        [
                            'type' => 'date',
                            'label' => false,
                            'class' => 'mb-2 py-1 text-secondary',
                            'style' => 'width:9rem;',
                            'value' => $relatorio->data ?? null,
                        ]
                    ) ?>
                </div>
                
            <?php elseif ($p->id == 35) : ?>
                <!-- Já foi renderizado o seletor do responsável junto do radio, acima 
            dessa forma não tem campo de observação -->
            <?php else : ?>
                <div class="pads">
                    <?= $this->Form->control(
                        "respostas.{$p->id}.observacao",
                        [
                            "type" => "textarea",
                            "rows" => 1,
                            "label" => false,
                            "class" => "mt-2 smart-textarea place-cor",
                            "placeholder" => "+ Adicionar observação",
                            "value" => $respostaSalva->observacao ?? '',
                        ]
                    ) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="border-start ms-2 px-2 d-flex flex-wrap align-items-center d-print-none">
            <div style="width:136px;">
                <div class="d-flex justify-content-center">
                    <i class="bi bi-check-circle me-2 fs-6 text-success"></i>
                    <div>
                        <p class="d-flex mb-0 fs-7 text-success lh-1">
                            Respondida
                        </p>
                        <p class="text-nowrap mb-0 fs-8 text-secondary">
                            em <?= $respostaSalva?->modified ?>
                        </p>
                    </div>
                </div>

                <?php
                $statusSalvo = $respostaSalva->status ?? 0;
                ?>
                <?php if ($p->tipo != "data"): ?>
                    <button
                        type="button"
                        class="btn-acompanhamento btn btn-<?= $statusSalvo ? 'success' : 'warning' ?> btn-sm btn-s-pill shadow fs-8 py-0"
                        data-pergunta-id="<?= $p->id ?>">
                        <div class="d-flex align-items-center">

                            <i class="bi bi-<?= $statusSalvo ? 'check' : 'exclamation' ?>-circle me-1 text-white fs-6 py-2 lh-1"></i>

                            <span class="lh-sm">
                                <?= $statusSalvo ? 'Requerido' : 'Requer' ?><br>
                                Acompanhamento
                            </span>

                        </div>
                    </button>
                <?php endif; ?>
            </div>

        </div>

    </div>
    <?php if ($p->id != 35 && $p->tipo != 'data'): ?>

        <?= $this->Form->hidden(
            "respostas.{$p->id}.status",
            ['value' => $statusSalvo]
        ) ?>
    <?php endif; ?>

<?php endforeach; ?>

<?= $this->Form->end() ?>
<script>
    document.querySelectorAll('.usuario-select').forEach(select => {
        // Define o comprimento ideal do select de acordo com a maior frase nas opções
        const options = select.querySelectorAll('.usuario-option');
        const selecionado = select.querySelector('.usuario-selecionado');
        const hidden = document.querySelector('[name="id_usuario"]');
        const display = select.querySelector('.usuario-select-display');
        const optionsContainer = select.querySelector('.usuario-select-options');
        // Define a largura pelo maior conteúdo:
        // placeholder ou maior option
        const larguraPlaceholder = display.scrollWidth;
        const larguraOptions = optionsContainer.scrollWidth;
        const maiorLargura = Math.max(larguraPlaceholder, larguraOptions);
        select.style.width = maiorLargura + 'px';
        options.forEach(option => {
            option.addEventListener('click', () => {
                selecionado.innerHTML = option.innerHTML;
                hidden.value = option.dataset.value;
                select.classList.remove('aberto');
            });
        });
        // Somente uma opção: seleciona automaticamente
        if (options.length === 1) {
            const option = options[0];
            selecionado.innerHTML = option.innerHTML;
            hidden.value = option.dataset.value;
            select.classList.add('read');
        }
        // Mostra as opções ao clicar
        display.addEventListener('click', () => {
            if (select.classList.contains('read')) return;
            select.classList.toggle('aberto');
        });
        // Fecha as opções caso o mouse deixe o perimetro
        optionsContainer.addEventListener('mouseleave', () => {
            select.classList.remove('aberto');
        });
    });
    // Controle sim/não para visualização do select
    const radioAcompanhou = document.querySelectorAll('input[name="resposta-rd35"]');
    const usuarioSelect = document.querySelector('.usuario-select');
    const hiddenUsuario = document.querySelector('[name="id_usuario"]');
    const selecionado = usuarioSelect.querySelector('.usuario-selecionado');
    const atualizarUsuarioSelect = () => {
        const radioSelecionado = document.querySelector('input[name="resposta-rd35"]:checked');
        if (radioSelecionado && radioSelecionado.value === '1')
            // Sim
            usuarioSelect.classList.remove('d-none');
        else {
            // Não ou nenhuma opção selecionada
            usuarioSelect.classList.add('d-none');
            // Limpa o valor enviado
            hiddenUsuario.value = '';
            // Limpa a apresentação visual
            selecionado.innerHTML = '⇩ Selecione quem acompanhou';
            // Garante que as options estejam fechadas
            usuarioSelect.classList.remove('aberto');
        }
    };
    radioAcompanhou.forEach(radio => {
        radio.addEventListener('change', atualizarUsuarioSelect);
    });
    // Verifica o estado inicial
    atualizarUsuarioSelect();
</script>