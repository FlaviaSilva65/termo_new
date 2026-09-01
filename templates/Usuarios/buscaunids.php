<?php foreach ($unids as $i => $unid): ?>

    <div class="col-4">
        <?= $unid->nm_unid_escolar ?>
    </div>
    <input type="hidden" name="setores_id" value="<?= $unid->setores_id ?>">
    <input type="hidden" name="unid_escolares_id" value="<?= $unid->id ?>">

<?php endforeach; ?>