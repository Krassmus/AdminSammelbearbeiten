<?php

class AdminSammelbearbeiten extends StudIPPlugin implements SystemPlugin, AdminCourseAction {

    public function getAdminActionURL()
    {
        return PluginEngine::getURL($this, array(), "bulk/edit");
    }

    public function useMultimode() {
        PageLayout::addScript($this->getPluginURL()."/assets/select2.min.js");
        PageLayout::addStylesheet($this->getPluginURL()."/assets/select2.min.css");
        if (version_compare($GLOBALS['SOFTWARE_VERSION'], "3.4.99", ">=")) {
            //Damit es im Dialog ge�ffnet wird
            return \Studip\Button::createAccept(_("Bearbeiten"), "edit", array('data-dialog' => 1));
        } else {
            return _("Bearbeiten");
        }
    }

    public function getAdminCourseActionTemplate($course_id, $values = null, $semester = null) {
        $factory = new Flexi_TemplateFactory(__DIR__."/views");
        $template = $factory->open("action/checkbox.php");
        $template->set_attribute("course_id", $course_id);
        $template->set_attribute("plugin", $this);
        return $template;
    }

}
