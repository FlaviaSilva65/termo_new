<div class="container">
    <?= $this->Form->create($setores) ?>

    <div class="row mt-5 mb-2 mx-0 d-flex justify-content-center">
        <div class="col-4">
            <?= $this->Form->control('nm_setor', ['label' => 'Setor']); ?>
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