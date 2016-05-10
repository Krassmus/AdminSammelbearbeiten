<style>
    .bulkedit tr {
        opacity: 0.5;
    }
    .bulkedit tr.active {
        opacity: 1;
    }
</style>
<? if (count($courses) > 0) : ?>
    <form action="<?= PluginEngine::getLink($plugin, array(), "bulk/edit") ?>" method="post" class="default bulkedit">
        <? foreach ($sem_ids as $seminar_id) : ?>
            <input type="hidden" name="sem_ids[]" value="<?= htmlReady($seminar_id) ?>">
        <? endforeach ?>
        <table class="default nohover">
            <tbody>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="teilnehmer" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Maximale Teilnehmerzahl") ?>
                        </label>
                    </td>
                    <td>
                        <? $value = $controller->getAverageValue($courses, "teilnehmer") ?>
                        <input type="text"
                               name="teilnehmer"
                               value="<?= htmlReady($value)?>"
                               placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="ects" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("ECTS-Punkte") ?>
                        </label>
                    </td>
                    <td>
                        <? $value = $controller->getAverageValue($courses, "ects") ?>
                        <input type="text"
                               name="ects"
                               value="<?= htmlReady($value)?>"
                               placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>">
                    </td>
                </tr>
                <? if ($GLOBALS['perm']->have_perm("admin")) : ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="status" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Veranstaltungstyp") ?>
                        </label>
                    </td>
                    <td>
                        <?
                        $sem_types = array();
                        foreach (SemClass::getClasses() as $sc) {
                            if (!$sc['course_creation_forbidden'] || $GLOBALS['perm']->have_perm("root")) {
                                foreach ($sc->getSemTypes() as $st) {
                                    $sem_types[$st['id']] = $st['name'] . ' (' . $sc['name'] . ')';
                                }
                            }
                        } ?>
                        <? $value = $controller->getAverageValue($courses, "status") ?>
                        <select name="status">
                            <? if (!$value) : ?>
                                <option value=""><?= ($value === false ? " - " ._("Unterschiedliche Werte")." - " : _(" - ")) ?></option>
                            <? endif ?>
                            <? foreach ($sem_types as $sem_type_id => $sem_type_name) : ?>
                                <option value="<?= htmlReady($sem_type_id) ?>"<?= $sem_type_id == $value ? " selected" : "" ?>>
                                    <?= htmlReady($sem_type_name) ?>
                                </option>
                            <? endforeach ?>
                        </select>
                    </td>
                </tr>
                <? endif ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="visible" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Sichtbar") ?>
                        </label>
                    </td>
                    <td>
                        <? $value = $controller->getAverageValue($courses, "visible") ?>
                        <input type="checkbox"
                               name="visible"
                               value="1"
                               <?= $value == 1 ? " checked" : ""?>>
                        <? if ($value === false) : ?>
                            <div><?= _("Unterschiedliche Werte") ?></div>
                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="locked" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Gesperrt") ?>
                        </label>
                    </td>
                    <td>
                        <?
                        $value = null;
                        foreach ($courses as $course) {
                            $seminar = new Seminar($course->getId());
                            $coursevalue = $seminar->isAdmissionLocked() ? 1 : 0;
                            if ($value === null) {
                                $value = $coursevalue;
                            } elseif($value != $coursevalue) {
                                $value = false;
                            }
                        }
                        ?>
                        <input type="checkbox"
                               name="locked"
                               value="1"
                            <?= $value == 1 ? " checked" : ""?>>
                        <? if ($value === false) : ?>
                            <div><?= _("Unterschiedliche Werte") ?></div>
                        <? endif ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div data-dialog-button>
            <?= \Studip\Button::create(_("Speichern"), "save") ?>
        </div>
    </form>
<? endif ?>