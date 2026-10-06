<?php if ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5 || $identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 8) {
    $finalizarPendAction = 'dash_escolas';
    $parametro = $escola_id ? $escola_id : $escola->id;
} elseif ($identity->tp_usuarios_id == 2) {
    $finalizarPendAction = 'dash_supervisor';
    $parametro = $identity->id;
}
?>
<?php if ($somentePendencias) { ?>
    <div class="col-12 col-lg-11 d-flex mx-auto mt-2 justify-content-center">
        <h6 class="badge rounded-pill bg-danger-subtle text-danger px-5 py-2">
            ACOMPANHAMENTO DE PENDÊNCIAS.
        </h6>
    </div>
<?php } ?>
<div class="col-12 col-lg-11 d-flex mx-auto">

    <?=
    $this->element('secoes_termo') .
        '<div class="w-100 p-0 p-lg-2">' .
        $this->element('perguntas') ?>
    <div class="bg-white p-2 rounded-bottom shadow d-flex justify-content-between align-items-center d-print-none">
        <?php if (empty($somentePendencias)) : ?>
            <?php /* $this->Html->link(
                '<i class="bi bi-arrow-left"></i>Etapa enterior',
                ['action' => 'manterPerguntas', $dimensao - 1, $escola_id, $relatorio->id],
                ['class' => 'btn btn-primary btn-sm btn-s-pill shadow link-navegacao ' . $desabilitaAnt . '', 'escape' => false]
            )*/ ?>

            <?= $this->element('btn_pill', [
                'label' => 'Etapa enterior',
                'cor' => 'primary',
                'icon'  => 'arrow-left',
                'url'   => ['action' => 'manterPerguntas', $dimensao - 1, $escola_id, $relatorio->id],
                'class' => 'btn-pill--fixed link-navegacao ' . $desabilitaAnt . '',
            ]) ?>

            <div class="">
                <?php if (empty($modoSomenteLeitura)): ?>
                    <?php /* $this->Html->link(
                        '<i class="bi bi-floppy me-2"></i>Salvar rascunho',
                        '/',
                        ['class' => 'btn btn-success btn-sm btn-s-pill shadow me-3 btn-salvar-rascunho ' . $desabilitaSalvarRasc . '', 'escape' => false]
                    ) */ ?>

                    <?= $this->element('btn_pill', [
                        'label' => 'Salvar rascunho',
                        'cor' => 'success',
                        'icon'  => 'floppy',
                        'url'   => ['/'],
                        'class' => 'btn-pill--fixed btn-salvar-rascunho' . $desabilitaSalvarRasc . '',
                    ]) ?>
                <?php endif; ?>
                <?php /* $this->Html->link(
                    'Próxima etapa<i class="bi bi-arrow-right ms-2"></i>',
                    ['action' => 'manterPerguntas', $dimensao + 1, $escola_id, $relatorio->id],
                    ['class' => 'btn btn-primary btn-sm btn-e-pill shadow link-navegacao ' . $desabilitaProx . '', 'escape' => false]
                ) */ ?>

                <?= $this->element('btn_pill', [
                    'label' => 'Próxima etapa',
                    'cor' => 'primary',
                    'icon'  => 'arrow-right',
                    'position' => 'end',
                    'url'   => ['action' => 'manterPerguntas', $dimensao + 1, $escola_id, $relatorio->id],
                    'class' => 'btn-pill--fixed link-navegacao' . $desabilitaProx . '',
                ]) ?>

            </div>
        <?php else: ?>

            <?php /* $this->Html->link(
                '<i class="bi bi-arrow-left me-2"></i>Voltar ao painel',
                ['action' => 'dash-supervisor', $identity->id],
                ['class' => 'btn btn-primary btn-sm btn-s-pill shadow', 'escape' => false]
            ) */ ?>

            <?= $this->element('btn_pill', [
                'label' => 'Voltar ao painel',
                'cor' => 'primary',
                'icon'  => 'arrow-left',
                'url'   => ['action' => 'dash-supervisor', $identity->id],
                'class' => 'btn-pill--fixed',
            ]) ?>

            <div class="">
                <?php
                $proximaComPendencia = null;
                foreach ($dimensoesComPendencia as $d) {
                    if ($d > $dimensao) {
                        $proximaComPendencia = $d;
                        break;
                    }
                }
                ?>
                <?php if ($proximaComPendencia): ?>
                    <?php /* $this->Html->link(
                        'Próxima pendência<i class="bi bi-arrow-right ms-2"></i>',
                        ['action' => 'manterPerguntas', $proximaComPendencia, $escola_id, $relatorio->id, '?' => ['pendencias' => 1]],
                        ['class' => 'btn btn-primary btn-sm btn-e-pill shadow link-secao', 'escape' => false]
                    ) */ ?>


                    <?= $this->element('btn_pill', [
                        'label' => 'Próxima pendência',
                        'cor' => 'primary',
                        'icon'  => 'arrow-right',
                        'position' => 'end',
                        'url'   => ['action' => 'manterPerguntas', $proximaComPendencia, $escola_id, $relatorio->id, '?' => ['pendencias' => 1]],
                        'class' => 'btn-pill--fixed link-secao',
                    ]) ?>


                <?php else: ?>
                    <?php /* $this->Html->link(
                        '<i class="bi bi-check-circle me-2"></i>Finalizar pendências',
                        ['action' => $finalizarPendAction, $parametro],
                        ['class' => 'btn btn-success btn-sm btn-s-pill shadow me-3', 'escape' => false]
                    ) */ ?>

                    <?= $this->element('btn_pill', [
                        'label' => 'Finalizar pendências',
                        'cor' => 'success',
                        'icon'  => 'check-circle',
                        'url'   => ['action' => $finalizarPendAction, $parametro],
                        'class' => 'btn-pill--fixed',
                    ]) ?>

                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    const somentePendencias = <?= $somentePendencias ? 'true' : 'false' ?>;
    const modoSomenteLeitura = <?= !empty($modoSomenteLeitura) ? 'true' : 'false' ?>;
    const relatorioId = <?= (int)$relatorio->id ?>;

    // Radio
    function atualizaResposta(radio) {
        const grupo = radio.closest('.grupo-resposta');
        // Limpa somente os radios desta pergunta
        grupo.querySelectorAll('label').forEach(function(label) {
            label.classList.remove('resposta-verde', 'resposta-vermelho', 'resposta-amarelo', 'resposta-laranja', 'resposta-azul');
        });
        const label = radio.closest('label');
        const resposta = label.textContent.trim().toLowerCase();
        const classes = {
            'sim': 'resposta-verde',
            'não': 'resposta-vermelho',
            'não verificado': 'resposta-amarelo',
            'adequado': 'resposta-verde',
            'inadequado': 'resposta-vermelho',
            'parcialmente adequado': 'resposta-laranja',
            'não se aplica': 'resposta-azul',
        };
        if (classes[resposta]) label.classList.add(classes[resposta]);
    }
    document.querySelectorAll('.grupo-resposta input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            atualizaResposta(this);
        });
        radio.addEventListener('click', function() {
            this.blur();
        });
    });
    document.querySelectorAll('.grupo-resposta input[type="radio"]:checked').forEach(function(radio) {
        atualizaResposta(radio);
    });

    // checkbox
    document.querySelectorAll('.checkbox-resposta input[type="checkbox"]').forEach(function(checkbox) {
        function atualizaCheckbox() {
            checkbox.closest('label').classList.toggle('resposta-verde', checkbox.checked);
        }
        checkbox.addEventListener('change', atualizaCheckbox);
        // Aplica a aparência caso já esteja marcado ao carregar
        atualizaCheckbox();
    });

    function erroObservacao(perguntaId, mostrar) {
        const campo = document.querySelector(`[name="respostas[${perguntaId}][observacao]"]`);
        const msg = document.getElementById(`erro-observacao-${perguntaId}`);

        if (campo) campo.classList.toggle('is-invalid', mostrar);
        if (msg) msg.classList.toggle('d-block', mostrar);
    }

    document.querySelectorAll('.btn-acompanhamento').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const perguntaId = this.dataset.perguntaId;
            const hidden = document.querySelector(`input[name="respostas[${perguntaId}][status]"]`);
            const observacao = document.querySelector(
                `[name="respostas[${perguntaId}][observacao]"]`
            );
            const novoStatus = hidden.value == '1' ? '0' : '1';

            if (novoStatus === '0') {
                if (!observacao || observacao.value.trim() === '') {
                    erroObservacao(perguntaId, true);

                    if (observacao) {
                        observacao.focus();
                        observacao.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return;
                }
            }

            // Remove indicação de erro
            if (observacao) {
                observacao.classList.remove('is-invalid');
            }

            const icon = this.querySelector('.btn-pill__icon i');
            const texto = this.querySelector('.js-acomp-texto');
            const btnEl = this;

            btnEl.disabled = true; // evita clique duplo durante o request

            const csrfToken = document.querySelector('input[name="_csrfToken"]')?.value;

            fetch('/termo/relatorios/atualizarStatusResposta', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        relatorio_id: relatorioId,
                        pergunta_id: perguntaId,
                        status: novoStatus,
                        observacao: observacao ? observacao.value.trim() : '',
                        _csrfToken: csrfToken || '',
                    }),
                })
                .then(async res => {
                    const resposta = await res.text();

                    console.log('Status HTTP:', res.status);
                    console.log('Resposta do servidor:', resposta);

                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status + ': ' + resposta);
                    }

                    return JSON.parse(resposta);
                })
                .then(data => {
                    if (!data.success) {
                        alert(data.message ||
                            'Não foi possível atualizar o status. Tente novamente.'
                        );
                        if (data.campo === 'observacao' && observacao) {
                            observacao.classList.add('is-invalid');
                            observacao.focus();
                        }
                        return;
                    }

                    hidden.value = novoStatus;

                    const marcado = novoStatus === '1';

                    btnEl.classList.toggle('btn-success', marcado);
                    btnEl.classList.toggle('btn-dark-warning', !marcado);

                    icon.classList.toggle('bi-check-circle', marcado);
                    icon.classList.toggle('bi-exclamation-circle', !marcado);

                    texto.textContent = marcado ?
                        (somentePendencias ? 'Resolvido' : 'Requerido') :
                        'Requer';
                })
                .catch(err => {
                    console.error('Erro ao atualizar status:', err);
                    alert('Erro de conexão ao atualizar status.');
                })
                .finally(() => {
                    btnEl.disabled = false;
                });
        });
    });

    document.querySelectorAll(
        'textarea[name^="respostas"][name$="[observacao]"]'
    ).forEach(function(textarea) {

        textarea.addEventListener('input', function() {

            if (this.value.trim() !== '') {
                const m = this.name.match(/^respostas\[(\d+)\]/);
                if (m) erroObservacao(m[1], false);
            }

        });

    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-perguntas'); // form principal (dentro do element perguntas)

        if (!form) return;

        function salvarEIrPara(url, ficarNaPagina = true) {
            const formData = new FormData(form);
            // formData.append('_ajax', '1'); // marcador explícito

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        // 'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            console.error('Resposta não-OK:', res.status, text);
                            throw new Error('HTTP ' + res.status);
                        });
                    }
                    return res.json();
                })

                .then(data => {
                    if (data.success) {
                        atualizarSecoes(data.contagem);
                        if (url) {
                            window.location.href = url;
                        } else if (ficarNaPagina) {
                            mostrarFeedback('Rascunho salvo com sucesso!');
                        }
                    } else {
                        mostrarFeedback('Erro ao salvar. Tente novamente.', true);
                    }
                })
                .catch(err => {
                    console.error('Erro no fetch:', err);
                    mostrarFeedback('Erro de conexão.', true);
                });
        }

        function mostrarFeedback(msg, erro = false) {
            // Troque por um toast/notificação do seu design system
            alert(msg);
        }

        // Seções (coluna esquerda)
        document.querySelectorAll('.link-secao').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (somentePendencias || modoSomenteLeitura) {
                    window.location.href = this.href;
                } else {
                    salvarEIrPara(this.href);
                }

            });
        });

        // Etapa anterior / Próxima etapa
        document.querySelectorAll('.link-navegacao').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (modoSomenteLeitura) {
                    window.location.href = this.href;
                } else {
                    salvarEIrPara(this.href);
                }
            });
        });

        // Salvar rascunho — não navega
        const btnSalvar = document.querySelector('.btn-salvar-rascunho');
        if (btnSalvar) {
            btnSalvar.addEventListener('click', function(e) {
                e.preventDefault();
                salvarEIrPara(null, true);
            });
        }
    });

    function atualizarSecoes(contagem) {
        if (!contagem) return;
        document.querySelectorAll('.progress-ring[data-dimensao]').forEach(function(ring) {
            const dim = ring.dataset.dimensao;
            if (contagem.hasOwnProperty(dim)) {
                const novoAtual = parseInt(contagem[dim]);
                ring.dataset.current = novoAtual;
                renderProgress(ring, novoAtual, parseInt(ring.dataset.total));
            }
        });
        atualizaProgressBar();
    }
</script>