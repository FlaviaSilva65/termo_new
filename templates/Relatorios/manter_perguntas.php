<div class="col-12 col-lg-11 d-flex mx-auto">
    <?=
    $this->element('secoes_termo') .
        '<div class="w-100 p-0 p-lg-2">' .
        $this->element('perguntas') .
        '<div class="bg-white p-2 rounded-bottom shadow d-flex justify-content-between align-items-center d-print-none">' .
        $this->Html->link(
            '<i class="bi bi-arrow-left me-2"></i>Etapa enterior',
            ['action' => 'manterPerguntas', $dimensao - 1, $escola_id, $relatorio->id],
            ['class' => 'btn btn-primary btn-sm btn-s-pill shadow link-navegacao '.$desabilitaAnt.'', 'escape' => false]
        ) .
        '<div class="">' .
        $this->Html->link(
            '<i class="bi bi-floppy me-2"></i>Salvar rascunho',
            '/',
            ['class' => 'btn btn-success btn-sm btn-s-pill shadow me-3 btn-salvar-rascunho', 'escape' => false]
        ) .
        $this->Html->link(
            'Próxima etapa<i class="bi bi-arrow-right ms-2"></i>',
            ['action' => 'manterPerguntas', $dimensao + 1, $escola_id, $relatorio->id],
            ['class' => 'btn btn-primary btn-sm btn-e-pill shadow link-navegacao '.$desabilitaProx.'', 'escape' => false]
        ) .
        '</div>
            </div>
        </div>'
    ?>
</div>
<script>
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

    document.querySelectorAll('.btn-acompanhamento').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const perguntaId = this.dataset.perguntaId;
            const hidden = document.querySelector(`input[name="respostas[${perguntaId}][status]"]`);
            const novoStatus = hidden.value == '1' ? '0' : '1';
            hidden.value = novoStatus;

            const icon = this.querySelector('i');
            const span = this.querySelector('span');

            if (novoStatus === '1') {
                this.classList.replace('btn-warning', 'btn-success');
                icon.classList.replace('bi-exclamation-circle', 'bi-check-circle');
                span.innerHTML = 'Requerido<br>Acompanhamento';
            } else {
                this.classList.replace('btn-success', 'btn-warning');
                icon.classList.replace('bi-check-circle', 'bi-exclamation-circle');
                span.innerHTML = 'Requer<br>Acompanhamento';
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
                salvarEIrPara(this.href);
            });
        });

        // Etapa anterior / Próxima etapa
        document.querySelectorAll('.link-navegacao').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                salvarEIrPara(this.href);
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