<style>
    .bulkedit tbody tr {
        opacity: 0.5;
    }
    .bulkedit tbody tr.active {
        opacity: 1;
    }
    .bulkedit .entsperren_hinweis {
        display: none;
    }
    .bulkedit tbody tr.active input:not(:checked) + .entsperren_hinweis {
        display: block;
    }
</style>
<? if (count($courses) > 0) : ?>
    <form action="<?= PluginEngine::getLink($plugin, array(), "bulk/edit") ?>"
          method="post"
          class="default bulkedit">
        <? foreach ($sem_ids as $seminar_id) : ?>
            <input type="hidden" name="sem_ids[]" value="<?= htmlReady($seminar_id) ?>">
        <? endforeach ?>
        <table class="default nohover">
            <thead>
                <tr>
                    <th width="50%"><?= _("Zu verändernde Eigenschaft auswählen") ?></th>
                    <th width="50%"><?= _("Neuen Wert festlegen") ?></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <label>
                        <input type="checkbox" name="change[]" value="veranstaltungsnummer" onChange="jQuery(this).closest('tr').toggleClass('active');">
                        <?= _("Veranstaltungsnummer") ?>
                    </label>
                </td>
                <td>
                    <? $value = $controller->getAverageValue($courses, "veranstaltungsnummer") ?>
                    <input type="text"
                           name="veranstaltungsnummer"
                           value="<?= htmlReady($value)?>"
                           placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                </td>
            </tr>
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
                           placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
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
                               placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                               onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
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
                        <select name="status"
                                onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
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
                               <?= $value == 1 ? " checked" : ""?>
                               onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                        <? if ($value === false) : ?>
                            <div><?= _("Unterschiedliche Werte") ?></div>
                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="access" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Zugriffsrechte") ?>
                        </label>
                    </td>
                    <td>
                        <?
                        $value = null;
                        foreach ($courses as $course) {
                            $seminar = new Seminar($course->getId());
                            $coursevalue = $seminar->isAdmissionLocked() ? "locked" : null;
                            if ($coursevalue === null) {
                                $coursevalue = !$course['lesezugriff'] && !$course['schreibzugriff']
                                    ? "writable"
                                    : (!$course['lesezugriff'] ? "readable" : null);
                            }
                            if ($coursevalue === null) {
                                $coursevalue = $seminar->getCourseSet() === null ? "unlocked" : null;
                            }
                            if ($value === null) {
                                $value = $coursevalue;
                            } elseif($value != $coursevalue) {
                                $value = false;
                            }
                        }
                        ?>
                        <select name="access" onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                            <option value=""><?= $value === false ? _("Unterschiedliche Werte") : "" ?></option>
                            <option value="locked"<?= $value === "locked" ? " selected" : "" ?>><?= _("Gesperrt") ?></option>
                            <option value="unlocked"<?= $value === "unlocked" ? " selected" : "" ?>><?= _("Entsperrt") ?></option>
                            <option value="writable"<?= $value === "writable" ? " selected" : "" ?>><?= _("Öffentlicher Schreib- und Lesezugriff") ?></option>
                            <option value="readable"<?= $value === "readable" ? " selected" : "" ?>><?= _("Öffentlicher Lesezugriff") ?></option>
                        </select>
                    </td>
                </tr>
                <? if (count($userdomains)) : ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="change[]" value="userdomains" onChange="jQuery(this).closest('tr').toggleClass('active');">
                                <?= _("Domänen") ?>
                            </label>
                        </td>
                        <td>
                            <? $value = $controller->getAverageValue($courses, "userdomains") ?>
                            <select name="userdomains[]"
                                    onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');"
                                    multiple>
                                <? if ($value === false) : ?>
                                    <option value="" selected><?= ($value === false ? " - " ._("Unterschiedliche Werte")." - " : _(" - ")) ?></option>
                                <? endif ?>
                                <? foreach ($userdomains as $userdomain) : ?>
                                    <option value="<?= htmlReady($userdomain->getID()) ?>"<?= in_array($userdomain->getID(), (array) $value) ? " selected" : "" ?>>
                                        <?= htmlReady($userdomain['name']) ?>
                                    </option>
                                <? endforeach ?>
                            </select>
                        </td>
                    </tr>
                <? endif ?>
                <? if (count($lockrules)) : ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="change[]" value="lock_rule" onChange="jQuery(this).closest('tr').toggleClass('active');">
                                <?= _("Sperrebenen") ?>
                            </label>
                        </td>
                        <td>
                            <? $value = $controller->getAverageValue($courses, "lock_rule") ?>
                            <select name="lock_rule"
                                    onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                                <? if (!$value) : ?>
                                    <option value=""><?= ($value === false ? " - " ._("Unterschiedliche Werte")." - " : _(" - ")) ?></option>
                                <? endif ?>
                                <option value="none"><?= _("Sperrebenen entfernen") ?></option>
                                <? foreach ($lockrules as $lockrule) : ?>
                                    <option value="<?= htmlReady($lockrule->getId()) ?>"<?= $lockrule->getId() == $value ? " selected" : "" ?>>
                                        <?= htmlReady($lockrule['name']) ?>
                                    </option>
                                <? endforeach ?>
                            </select>
                        </td>
                    </tr>
                <? endif ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" name="change[]" value="start_time" onChange="jQuery(this).closest('tr').toggleClass('active');">
                            <?= _("Startsemester") ?>
                        </label>
                    </td>
                    <td>
                        <? $value = $controller->getAverageValue($courses, "start_time") ?>
                        <select name="start_time"
                                onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                            <? if (!$value) : ?>
                                <option value=""><?= ($value === false ? " - " ._("Unterschiedliche Werte")." - " : _(" - ")) ?></option>
                            <? endif ?>
                            <? foreach (Semester::getAll() as $semester) : ?>
                                <option value="<?= htmlReady($semester['beginn']) ?>"<?= $semester['beginn'] == $value ? " selected" : "" ?>>
                                    <?= htmlReady($semester['name']) ?>
                                </option>
                            <? endforeach ?>
                        </select>
                    </td>
                </tr>
                <? foreach ($datafields as $datafield) : ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="change[]" value="datafield_<?= $datafield->getId() ?>" onChange="jQuery(this).closest('tr').toggleClass('active');">
                                <?= htmlReady($datafield['name']) ?>
                            </label>
                        </td>
                        <td>
                            <? $value = $controller->getAverageValue($courses, "datafield_".$datafield->getId()) ?>
                            <? switch ($datafield->type) {
                                case "bool" : ?>
                                    <input type="checkbox"
                                           name="datafield_<?= $datafield->getId() ?>"
                                           value="1"
                                           title="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');"
                                        <?= $value > 0 ? ' checked' : "" ?>>
                                <? break; case "selectbox" : ?>
                                    <select
                                           name="datafield_<?= $datafield->getId() ?>"
                                           value="<?= htmlReady($value)?>"
                                           title="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">
                                        <? foreach (explode("\n", $datafield['typeparam']) as $param) : ?>
                                            <option value="<?= htmlReady($param) ?>"<?= $param == $value ? " selected" : "" ?>><?= htmlReady($param) ?></option>
                                        <? endforeach ?>
                                    </select>
                                <? break; case "textarea" : ?>
                                    <textarea
                                           name="datafield_<?= $datafield->getId() ?>"
                                           placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');"
                                        ><?= htmlReady($value) ?></textarea>
                                <? break; case "textline" : default : ?>
                                    <input type="text"
                                           name="datafield_<?= $datafield->getId() ?>"
                                           value="<?= htmlReady($value)?>"
                                           placeholder="<?= htmlReady($value || $value === '0' ? $value : ($value === false ? _("Unterschiedliche Werte") : _("Wert eingeben")))?>"
                                           onChange="jQuery(this).closest('tr').addClass('active').find('td:first-child :checkbox').prop('checked', 'checked');">

                                <? } ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>
        </table>
        <div data-dialog-button>
            <?= \Studip\Button::create(_("Speichern"), "save") ?>
            <? if (!Request::isAjax()) : ?>
                <?= \Studip\LinkButton::create(_("Abbrechen"), URLHelper::getURL("dispatch.php/admin/courses")) ?>
            <? endif ?>
        </div>
    </form>
    <script>
        jQuery(function() {
            jQuery(".bulkedit select[multiple]").select2();
        });
    </script>
<? endif ?>
