<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class StaffDeletePage extends CRUDPage
{

    protected function prepareData(): void
    {
        parent::prepareData();
        $employeeId = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        $result = Staff::deleteById($employeeId);
        $this->redirect(self::ACTION_DELETE, $result);
    }


    protected function pageBody(): string
    {
        return "";
    }
}

$page = new StaffDeletePage();
$page->checkLogin($_SESSION['loggedIn']);