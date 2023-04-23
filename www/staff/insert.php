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
    private bool $isAdmin = true;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();
        $staffStuff = Staff::all();
        $this->workplaceDetails = $staffStuff[1];

        if($_SESSION['adminStatus'] == false)
        {
            //header('Location: ../logoff.php');
            header('HTTP/1.0 403 Forbidden');
            die;
        }

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $this->employee = new Staff();

                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                //načíst data
                $this->employee = Staff::readPost();

                foreach($_POST['room_keys'] as $item){
                    $workplace = array_search($item, array_column($this->workplaceDetails, 'room_id'));
                    if($workplace >= 0)
                    {
                        $this->workplaceDetails[$workplace]['checked'] = true;
                    }
                }

                $workplace = array_search($this->employee->room, array_column($this->workplaceDetails, 'room_id'));
                if($workplace >= 0)
                {
                    $this->workplaceDetails[$workplace]['selected'] = true;
                }

                //zkontrolovat data
                $this->errors = [];
                if ($this->employee->validate($this->errors) && $_SESSION['adminStatus'])
                {
                    //zpracovat
                    $result = $this->employee->insert();
                    //přesměrovat
                    $this->redirect(self::ACTION_INSERT, $result);
                }
                else if(!$_SESSION['adminStatus'])
                {
                    header("Location: ../logoff.php");
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
                'isAdmin' => $this->isAdmin,
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