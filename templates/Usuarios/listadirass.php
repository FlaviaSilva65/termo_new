<div class="container mb-4">

    <div class="row bg-success text-white py-1 my-3">
        <div class="col-1">
            <h5>RF</h5>
        </div>
        <div class="col-3">
            <h5>Nome</h5>
        </div>
        <div class="col-3" style="max-width: 240px; white-space: nowrap;overflow:hidden;text-overflow: ellipsis;">
            <h5>E-mail</h5>
        </div>
        <div class="col-1 text-center">
            <h5>Função</h5>
        </div>
        <div class="col-4 text-center">

        </div>
    </div>
    <?php foreach ($dir_assistentes as $dir_ass) : ?>

        <div class="row">
            <div class="col-1">
                <?= $dir_ass->rf ?>
            </div>
            <div class="col-3">
                <?= $dir_ass->nome ?>
            </div>
            <div class="col-3" style="max-width: 240px; white-space: nowrap;overflow:hidden;text-overflow: ellipsis;">
                <?= $dir_ass->email ?>
            </div>
            <div class="col-1">
                <?= $dir_ass->funcao ?>
            </div>
            <div class="col-4 text-center">

            </div>
        </div>

    <?php endforeach; ?>

</div>