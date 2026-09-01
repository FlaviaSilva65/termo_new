<div class="container">

    <?php foreach ($setores_unids as $i) : ?>

        <div class="row mt-5 mb-2 mx-0 d-flex justify-content-center">

        <?= $i ?>
            <?php foreach ($setores_unids->setore as $unid_escolare) : ?>

                <div class="col-4">
                    <?= $unid_escolare->unid_escolare->nm_unid_escolar ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

    <?= $this->Form->create($setor) ?>
    <div class="row mt-5 mb-2 mx-0 d-flex justify-content-center">
        <div class="col-4">
            <?= $this->Form->control('setores_id', ['label' => 'Setor', 'empty' => '---', 'options' => $setores]); ?>
        </div>
        <div class="col-4">
            <?= $this->Form->control('unid_escolares_id', ['label' => 'Unidade Escolar', 'empty' => '---', 'options' => $unid_escolares]); ?>
        </div>
    </div>


    <div class="row my-4 mx-0 d-flex justify-content-center">
        <div class="col-9">
            <div class="d-flex justify-content-center mt-4">
                <button class="btn btn-warning px-4 me-2" onclick="history.back()">
                    <i class="bi bi-arrow-return-left text-white"></i>
                </button>
                <button class="btn btn-success px-4 ms-2" type="submit"><i class="bi bi-save"></i> </button>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>