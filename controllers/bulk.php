<?

require_once "app/controllers/plugin_controller.php";

class BulkController extends PluginController
{
    public function edit_action()
    {
        $this->sem_ids = Request::getArray("sem_ids");
        if (count($this->sem_ids) === 0) {
            $this->sem_ids[] = Request::get("atleast");
        }
        $this->courses = Course::findMany($this->sem_ids);
        if (count($this->courses) === 0) {
            PageLayout::postMessage(MessageBox::error(_("Sie haben keine Veranstaltungen ausgewählt.")));
        }
        if (Request::isPost() && Request::submitted("save")) {
            $changes = Request::getArray("change");
            foreach ($this->courses as $course) {
                if ($GLOBALS['perm']->have_studip_perm("tutor", $course->getId())) {
                    foreach ($changes as $change) {
                        if ($change === "teilnehmer") {
                            $course['teilnehmer'] = Request::get("teilnehmer");
                        }
                        if ($change === "ects") {
                            $course['ects'] = Request::get("ects");
                        }
                        if ($change === "status" && Request::int("status") && $GLOBALS['perm']->have_perm("admin")) {
                            $course['status'] = Request::get("status");
                        }
                        if ($change === "visible") {
                            $course['visible'] = Request::int("visible", 0);
                        }
                        if ($change === "locked") {
                            $seminar = new Seminar($course->getId());
                            if (Request::get("locked")) {
                                if (!$seminar->isAdmissionLocked()) {
                                    DBManager::get()->exec("START TRANSACTION");
                                    $courseset = $seminar->getCourseSet();
                                    if ($courseset) {
                                        $courseset->removeCourse($course->getId());
                                        $courseset->store();
                                    }
                                    CourseSet::addCourseToSet(CourseSet::getGlobalLockedAdmissionSetId(), $course->getId());
                                    DBManager::get()->exec("COMMIT");
                                }
                            } else {
                                $courseset = $seminar->getCourseSet();
                                if ($courseset) {
                                    $courseset->removeCourse($course->getId());
                                    $courseset->store();
                                }
                            }
                        }
                    }
                    $course->store();
                }
            }
            PageLayout::postMessage(MessageBox::success(sprintf(_("%s Veranstaltungen erfolgreich bearbeitet."), count($this->courses))));
            $this->redirect(URLHelper::getURL("dispatch.php/admin/courses/index"));
        }
        PageLayout::setTitle(sprintf(_("Sammelbearbeiten von %s Veranstaltungen"), count($this->courses)));
    }

    public function getAverageValue($courses, $attribute) {
        $value = null;
        foreach ($courses as $course) {
            if ($value === null && $course[$attribute] !== '') {
                $value = $course[$attribute];
            } elseif($value != $course[$attribute]) {
                $value = false;
            }
        }
        return $value;
    }
}