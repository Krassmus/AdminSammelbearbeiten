<? if ($GLOBALS['perm']->have_studip_perm("tutor", $course_id)) : ?>
    <button type="submit"
            style="border: none; background: none; cursor: pointer;"
            data-dialog
            name="atleast"
            value="<?= $course_id ?>"
            title="<?= _("Ausgewählte Veranstaltungen bearbeiten") ?>">
        <? if (version_compare($GLOBALS['SOFTWARE_VERSION'], "3.5.99", "<")) : ?>
            <?= Assets::img("icons/20/blue/edit", array('class' => "text-bottom")) ?>
        <? else : ?>
            <?= Icon::create("edit", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
        <? endif ?>
    </button>
    <input type="checkbox" name="sem_ids[]" value="<?= htmlReady($course_id) ?>" class="text-top">
<? endif ?>
