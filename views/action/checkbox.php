<? if ($GLOBALS['perm']->have_studip_perm("tutor", $course_id)) : ?>
    <? if (version_compare($GLOBALS['SOFTWARE_VERSION'], "3.4.99", "<")) : ?>
        <button type="submit"
                style="border: none; background: none; cursor: pointer;"
                data-dialog
                name="atleast"
                value="<?= $course_id ?>"
                title="<?= _("Ausgewählte Veranstaltungen bearbeiten") ?>">
            <?= Assets::img("icons/20/blue/edit", array('class' => "text-bottom")) ?>
        </button>
    <? endif ?>
    <input type="checkbox" name="sem_ids[]" value="<?= htmlReady($course_id) ?>" class="text-top">

<? endif ?>
