<div class="col-12 <?= isset($num_col) ? $num_col : 'p-0' ?> <?= isset($col) ? $col : 'p-0' ?> mb-3">
    <div class="d-flex align-items-center justify-content-center bg-purple-40 rounded-top-5 text-white">
        <i class="bi bi-<?= $tit_icone ?> fs-4 me-4 py-2"></i>
        <h5 class="mb-0">
            <?= $titulo ?>
        </h5>
    </div>
    <div class=" p-0">
        <table class="w-100" style="border-collapse: separate; border-spacing: 0 10px;">
            <thead>
                <tr class="bg-light">
                    <th class="border border-card border-end-0 text-center"></th>
                    <th class="p-1 border border-card border-start-0 text-center">TERMO</th>
                    <th class="p-1 border border-card text-center">UNIDADE ESCOLAR</th>
                    <th class="p-1 border border-card border-end-1 text-center">DATA</th>
                    <?php if (($this->request->getParam('action') == 'dashSupervisor' || $this->request->getParam('action') == 'dashDiretorEscolas' || $this->request->getParam('action') == 'dashEscolas') && $ass_sub == '' && $r_ic == 0) { ?>

                        <th class="p-1 border border-card border-end-0 text-center">DIR</th>
                        <th class="p-1 border border-card border-end-0 text-center">AST</th>
                        <th class="p-1 border border-card border-end-0 text-center">SUB</th>
                        <th class="p-1 border border-card border-end-0 text-center"><i class="bi bi-eye fs-4 "></i></th>
                    <?php } else { ?>
                        <th class="p-1 border border-card border-start text-center "><i class="bi bi-eye fs-4"></i> </th>
                    <?php } ?>

                </tr>
            </thead>

            <?php if (!is_string($relatorios)) {



                $identityAuthentication = $this->request->getAttribute('authentication')
                    ?->getIdentity();

                $identityRequest = $this->request->getAttribute('identity');

                // debug([
                //     'authentication_identity_id' => $identityAuthentication?->getIdentifier(),
                //     'request_identity_id' => $identityRequest?->id,
                //     'request_identity_tp' => $identityRequest?->tp_usuarios_id,
                //     'impersonate' => $_SESSION['Impersonate'] ?? null,
                // ]);



                foreach ($relatorios as $relatorio) : ?>

                    <?php if (($identity->tp_usuarios_id == 2 || $identity->id == $relatorio->usuario_id)
                        && $relatorio->id_ass_dir == ''
                    ) {
                        // $params_edit = ['action' => 'add'];
                        $params_edit = ['action' => 'manterPerguntas'];
                        $params_edit[] = 1;
                        $params_edit[] = $relatorio->unid_escolar_id;
                        $params_edit[] = $relatorio->id;
                    }

                    $params = ['action' => $action];
                    if ($action == 'sign') {
                        $params[] = $relatorio->id;
                    } else {
                        $params[] = $relatorio->unid_escolar_id;
                        $params[] = $relatorio->id;
                    }
                    ?>

                    <!-- $r_ic => se é rascunho ou não; $ass_dir => Se tem assinatura diretor -->

                    <!-- Verifica quem estiver logado se é o Supervisor ou o Diretor -->

                    <?php
                    $usuario_valido = in_array($identity->tp_usuarios_id, [1, 2, 4, 5]);

                    // if (($identity->tp_usuarios_id == 2 && $relatorio->ic_rascunho == $r_ic && $relatorio->id_ass_sub == $ass_sub )
                    //     || (($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) && $relatorio->ic_rascunho == $r_ic && $relatorio->id_ass_sub == $ass_sub)
                    //     || ($identity->tp_usuarios_id == 4 && $relatorio->ic_rascunho == $r_ic && $relatorio->id_ass_sub == $ass_sub)
                    // ) 

                    // Variáveis de controle para exibição do registro específico neste card
                    $exibir_registro = false;
                    $aplicar_tachado = false;

                    if ($usuario_valido) {

                        if (isset($relatorio->ic_cancelado) && $relatorio->ic_cancelado == 1) {
                            if ($ass_sub === true) {
                                $exibir_registro = true;
                                $aplicar_tachado = true;
                            }
                        } else {
                            if ($relatorio->ic_rascunho == $r_ic && ($relatorio->id_ass_sub && $relatorio->id_ass_assis) == $ass_sub) {
                                $exibir_registro = true;
                            }
                        }
                    }

                    if ($exibir_registro
                        // $relatorio->ic_cancelado == 0 &&
                        // in_array($identity->tp_usuarios_id, [1, 2, 4, 5]) &&
                        // $relatorio->ic_rascunho == $r_ic &&
                        // $relatorio->id_ass_sub == $ass_sub 
                    ) { ?>
                        <tr class="hv bg-striped border-blue-15 text-color">
                            <td class="px-0 bg-<?= $relatorio->ic_prioridade == 1 && $cor ? 'danger' : 'blue-40' ?>"></td>
                            <td class="text-center p-0">
                                <span style="<?= $aplicar_tachado ? 'text-decoration: line-through;' : '' ?>">
                                    <?= $relatorio->termo_id ?>
                                </span>
                            </td>
                            <td class="text-start p-0">
                                <span style="<?= $aplicar_tachado ? 'text-decoration: line-through;' : '' ?>">
                                    <?= $relatorio->unid_escolare->sigla . ' ' . $relatorio->unid_escolare->nm_unid_escolar ?>
                                </span>
                                <?= $aplicar_tachado ? ' ' : '' ?>
                            </td>
                            <td class="text-center p-0">
                                <span style="<?= $aplicar_tachado ? 'text-decoration: line-through;' : '' ?>">
                                    <?= ($relatorio->data->i18nFormat("dd/MM")); ?>
                                </span>
                            </td>
                            <?php if (($this->request->getParam('action') == 'dashSupervisor' || $this->request->getParam('action') == 'dashDiretorEscolas' || $this->request->getParam('action') == 'dashEscolas') && $ass_sub == '' && $r_ic == 0) { ?>
                                <td class="text-center p-0">
                                    <?php if ($relatorio->id_ass_dir != '') { ?>

                                        <p><i class="bi bi-check fs-4 text-green-40"></i></p>
                                    <?php
                                    } ?>
                                </td>
                                <td class="text-center p-0">
                                    <?php if ($relatorio->id_ass_assis != '') { ?>

                                        <p><i class="bi bi-check fs-4 text-green-40"></i></p>

                                    <?php } ?>
                                </td>
                                <td class="text-center p-0">
                                    <?php if ($relatorio->id_ass_sub != '') { ?>

                                        <p><i class="bi bi-check fs-4 text-green-40"></i></p>

                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td class="me-0 p-0">
                                <div class="w-100 d-flex justify-content-end">
                                    <?php

                                    if (
                                        $identity->tp_usuarios_id == 2 && $relatorio->id_ass_dir == '' && $action != 'sign' ||
                                        $identity->tp_usuarios_id == 2 && $relatorio->id_ass_dir == '' && $params_edit && !$aplicar_tachado
                                    ) { ?>
                                        <?= $this->Html->link('<i class="bi bi-file-earmark-text fs-4 px-2 m-auto"></i>', $params_edit, [
                                            'class' => 'btn_edit btn btn-sm py-0 text-white d-flex text-center ' .
                                                ($relatorio->ic_prioridade == 1 && $cor ? 'btn-warning ' : (isset($cor_btn) ? 'btn-success' : 'btn-warning')),
                                            'data-bs-toggle' => 'popover',
                                            'data-bs-trigger' => 'hover focus',
                                            'data-bs-placement' => 'auto',
                                            'data-bs-content' => 'Editar Relatório',
                                            'style' => '',
                                            // 'target' => ($relatorio->ic_rascunho == 0 ?  '_blank' :  ''),
                                            'escape' => false
                                        ]); ?>
                                    <?php } else { ?>

                                        <?= $this->Html->link('<i class="bi bi-' . (isset($icone_btn) ? ($aplicar_tachado ? 'x-circle' : 'binoculars') : 'pencil-square') . ' fs-4 px-2 m-auto"></i>', $params, [
                                            'class' => 'btn_dash btn btn-sm py-0 text-white d-flex align-items-center ' .
                                                (($relatorio->ic_prioridade == 1 && $cor) || $aplicar_tachado ? 'btn-danger ' : (isset($cor_btn) ? 'btn-success' : ($relatorio->ic_rascunho == 1 ? 'btn-warning' : 'btn-blue-40'))),
                                            'data-bs-toggle' => 'popover',
                                            'data-bs-trigger' => 'hover focus',
                                            'data-bs-placement' => 'auto',
                                            'data-bs-content' => $relatorio->ic_prioridade == 1 && $cor ? 'Atenção Necessária' : 'Visualizar Termo',
                                            'style' => '',
                                            // 'target' => ($relatorio->ic_rascunho == 0 ?  '_blank' :  ''),
                                            'escape' => false,
                                            'onclick' => "window.location=this.href; return false;"
                                            // 'onclick' => ($relatorio->ic_rascunho == 0) ? "setTimeout(function(){ window.open('', '_self', ''); window.close(); }, 300);" : ""
                                        ]); ?>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php   }  ?>
            <?php endforeach;
            } ?>
        </table>
    </div>
    <div class="bg-purple-40 rounded-bottom-5" style="height: 53px;">
    </div>
</div>