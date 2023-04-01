<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class StaffInsertPage extends CRUDPage
{
    public string $title = "Přidat nového zaměstnance";
    protected int $state;
    private Staff $employee;
    private array $errors;
    private array $workplaceDetails;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();
        $staffStuff = Staff::all();
        $this->workplaceDetails = $staffStuff[1];

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $this->employee = new Staff();

                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                //načíst data
                $this->employee = Staff::readPost();
                //zkontrolovat data
                $this->errors = [];
                if ($this->employee->validate($this->errors))
                {
                    //zpracovat
                    $result = $this->employee->insert();
                    //přesměrovat
                    $this->redirect(self::ACTION_INSERT, $result);
                }
                else
                {
                    //na formulář
                    $this->state = self::STATE_FORM_REQUEST;
                }
                break;
        }
    }


    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("employee_form",
            [
                'employee' => $this->employee,
                'workplaceInfo' => $this->workplaceDetails,
                'errors' => $this->errors
            ]);
        //vyrenderuju
    }

    protected function getState() : int
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            return self::STATE_DATA_SENT;

        return self::STATE_FORM_REQUEST;
    }

}

$page = new StaffInsertPage();
$page->checkLogin($_SESSION['loggedIn']);