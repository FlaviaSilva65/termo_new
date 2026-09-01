<nav class="text-bg-primary py-2">
    <div class="position-absolute d-none end-0" style="bottom: 2.25rem">
        <button class="btn btn-link" data-bs-toggle="offcanvas" data-bs-target=".nav-offcanvas">
            <i class="bi bi-list fs-2" data-bs-toggle="tooltip" data-bs-trigger="hover focus" data-bs-title="Menu"></i>
        </button>
    </div>
    <div class="container px-sm-0">
        <ul class="nav nav-pills gap-1 flex-wrap flex-md-nowrap align-items-center" data-pushable>
            <?php
            $url = [
                'plugin' => null,
                'controller' => 'Relatorios'
            ];

            if (isset($identity) && $identity->tp_usuarios_id == 2 && $this->request->getParam('action') == 'dashEscolas' || ($this->request->getParam('action') == 'providencia')) {
                $urlPend = [
                    'plugin' => null,
                    // 'controller' => 'Providencias',
                    'action' => 'providencia'
                ];
                $urlPend[] = $escola->id;
            }

            if (isset($identity) && $identity->tp_usuarios_id == 2) {

                $url['action'] = 'dashSupervisor';
                $url[] = $setor->setores_id;
            } elseif (isset($identity) && $identity->tp_usuarios_id == 4) {

                $url['action'] = 'dashSubsecretaria';
            } elseif (isset($identity) && ($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5)) {

                $url['action'] = 'dashDiretorEscolas';
                $url[] = $identity->id;

                $urlPend = [
                    'plugin' => null,
                    // 'controller' => 'Providencias',
                    'action' => 'providencia'
                ];
                $urlPend[] = $escola->id;
            } elseif (isset($identity) && ($identity->tp_usuarios_id == 6 || $identity->tp_usuarios_id == 9)) {
                $url = [
                    'controller' => 'Usuarios',
                    'action' => 'index'
                ];
            }
            ?>
            <?php if (isset($identity)) {  ?>
                <li class="pushable-ignore order-1">
                    <a class="nav-link icon-link ativo" href="<?= $this->Url->build($url) ?>">
                        <i class="bi bi-house"></i>
                        <span class="d-none d-md-block">Início</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (isset($identity) && ($identity->tp_usuarios_id == 6 || $identity->tp_usuarios_id == 9)) { ?>

                <li class="dropdown pushable-ignore order-2">
                    <button class="nav-link icon-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="bi bi-person-workspace"></i>
                        <span class="d-none d-md-inline">Usuários</span>
                        <i class="bi bi-chevron-down smaller"></i>
                    </button>
                    <?php $url = [
                        'action' => 'add'
                    ]; ?>
                    <ul class="dropdown-menu">
                        <a class="dropdown-item icon-link" href="<?= $this->Url->build($url) ?>">
                            <i class="bi bi-person-add"></i>
                            <span>Cadastrar</span>
                        </a>
                    </ul>
                </li>
                <li class="dropdown pushable-ignore order-2">
                    <button class="nav-link icon-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="bi bi-people "></i>
                        <span class="d-none d-md-inline">Supervisores</span>
                        <i class="bi bi-chevron-down smaller"></i>
                    </button>

                    <?php $urlSupervisor = [
                        'plugin' => null,
                        'controller' => 'Impersonacao',
                        'action' => 'assumir'
                    ]; ?>

                    <ul class="dropdown-menu">
                        <?php foreach ($supervisores as $supervisorId => $supervisorName): ?>
                            <a class="dropdown-item icon-link" href="<?= $this->Url->build(array_merge($urlSupervisor, [$supervisorId])) ?>">
                                <i class="bi bi-person-gear"></i>
                                <span><?= h($supervisorName) ?> </span>
                            </a>
                        <?php endforeach; ?>
                    </ul>

                </li>

            <?php } ?>
            <?php if (isset($identity) && (($identity->tp_usuarios_id == 2)  || (($identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5) && count($escolas) > 1))) {  ?>
                <li class="dropdown pushable-ignore order-1">
                    <button class="nav-link icon-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="bi bi-building-fill "></i>
                        <span class="d-none d-md-inline">Escolas</span>
                        <i class="bi bi-chevron-down smaller"></i>
                    </button>

                    <?php $urlEscola = [
                        'plugin' => null,
                        'controller' => 'Relatorios',
                        'action' => 'dashEscolas'
                    ]; ?>

                    <ul class="dropdown-menu">
                        <?php foreach ($escolas as $escolaId => $escolaName): ?>
                            <a class="dropdown-item icon-link" href="<?= $this->Url->build(array_merge($urlEscola, [$escolaId])) ?>">
                                <i class="bi bi-building"></i>
                                <span>E.M. <?= h($escolaName) ?> </span>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                </li>

            <?php } elseif (isset($identity) && ($identity->tp_usuarios_id == 9)) { ?>
                <?php $urlEscola = [
                    'plugin' => null,
                    'controller' => 'UnidEscolares',
                    'action' => 'index'
                ]; ?>
                <li class="dropdown pushable-ignore order-3">
                    <a class="nav-link icon-link" href="<?= $this->Url->build($urlEscola) ?>">
                        <i class="bi bi-building"></i>
                        <span class="d-none d-md-block">Escolas</span>
                    </a>
                </li>
            <?php } ?>
            <?php $urlAno = [
                'plugin' => null,
                'controller' => 'Relatorios',
                'action' => 'dashRelatoriosAnteriores'
            ]; ?>
            <?php if (isset($escola)) : ?>

                <?php
                $urlAdd['action'] = 'add';
                $urlAdd[] = $escola->id;
                ?>
                <div class="w-100 d-md-none order-3"></div>
                <li class="pushable-ignore d-flex align-items-center justify-content-center gap-2 w-100 w-md-auto order-3 ms-md-auto me-md-auto mt-2 mt-md-0">

                    <span class="nav-link icon-link ativo pe-none text-truncate px-2" style="max-width: 260px; font-size: 0.9rem;">
                        <i class="bi bi-building"></i>
                        <span>E.M. <?= h($escola->nm_unid_escolar) ?> </span>
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <?php if (isset($identity) && ($identity->tp_usuarios_id == 2)) { ?>
                            <a class="nav-link icon-link ativo bg-success" href="<?= $this->Url->build($urlAdd) ?>">
                                <i class="bi bi-file-earmark-plus ms-0 ms-md-2"></i>
                                <span class="d-none d-md-inline">Novo Termo</span>
                            </a>
                        <?php } ?>
                        <?php if (isset($pendencias) && count($pendencias) > 0) { ?>
                            <a class="nav-link ativo icon-link" href="<?= $this->Url->build($urlPend) ?>">
                                <i class="bi bi-exclamation-diamond text-light"></i>
                                <span>Pendências</span><small class="badge bg-danger"><?= count($pendencias) ?></small>
                            </a>
                        <?php } ?>
                        <div class="dropdown">
                            <button class="nav-link icon-link ativo" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <i class="bi bi-calendar-range"></i>
                                <span>Acervo</span>
                                <i class="bi bi-chevron-down smaller"></i>
                            </button>
                            <?php if (isset($identity) && ($identity->tp_usuarios_id == 2 || $identity->tp_usuarios_id == 1 || $identity->tp_usuarios_id == 5)) { ?>

                                <ul class="dropdown-menu">

                                    <a class="dropdown-item icon-link" href="<?= $this->Url->build(array_merge($urlAno, [$years, $escola->id])) ?>">
                                        <?php $escola['id'] ?>
                                        <span><?= h($years) ?></span>
                                    </a>
                                </ul>
                            <?php } elseif (isset($identity) && ($identity->tp_usuarios_id == 3 || $identity->tp_usuarios_id == 4 || $identity->tp_usuarios_id == 7)) ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($years as $yearsId => $yearsName): ?>
                                    <a class="dropdown-item icon-link" href="<?= $this->Url->build(array_merge($urlAno, [$years, $escola->id])) ?>">
                                        <?php $escola['id'] ?>
                                        <span><?= h($years) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </li>
            <?php endif; ?>

            <!-- <li class="dropdown pushable-ignore ms-auto order-2 order-md-4">
                <button class="nav-link icon-link" data-theme-select data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="bi bi-circle-half" data-theme-icon></i> -->
            <!-- <i class="bi bi-chevron-down smaller"></i> -->
            <!-- </button>
                <ul class="dropdown-menu">
                    <button type="button" class="dropdown-item icon-link" value="light" data-theme-option>
                        <i class="bi bi-sun-fill" data-theme-icon></i>
                        <span>Light</span>
                    </button>
                    <button type="button" class="dropdown-item icon-link" value="dark" data-theme-option>
                        <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                        <span>Dark</span>
                    </button>
                    <button type="button" class="dropdown-item icon-link" value="auto" data-theme-option>
                        <i class="bi bi-circle-half" data-theme-icon></i>
                        <span>Auto</span>
                    </button>
                </ul>
            </li> -->

            <?php if (isset($identity)) {  ?>
                <li class="dropdown pushable-ignore ms-auto order-2 order-md-5">
                    <button class="nav-link icon-link ativo" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-block"><?= $funcao->nm_tp_usuarios ?>&nbsp;<?= explode(' ', $identity->nm_usuario)[0] ?></span>
                        <i class="bi bi-chevron-down smaller"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <span class="dropdown-item-text icon-link">
                            <i class="bi bi-person-circle d-none"></i>
                            <span>Olá, <?= $funcao->nm_tp_usuarios ?>&nbsp;<?= explode(' ', $identity->nm_usuario)[0] ?></span>
                        </span>

                        <hr class="dropdown-divider">

                        <a class="nav-link icon-link link-primary bg-light" href="<?= $this->Url->build(['plugin' => null, 'controller' => 'Usuarios', 'action' => 'logout']) ?>">
                            <i class="bi bi-door-open"></i>
                            <span>Sair</span>
                        </a>
                    </div>
                </li>

            <?php } ?>
        </ul>
    </div>

</nav>