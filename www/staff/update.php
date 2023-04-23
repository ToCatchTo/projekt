<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class StaffInsertPage extends CRUDPage
{
    public string $title = "Upravit zaměstnance";
    protected int $state;
    private Staff $employee;
    private array $errors;
    private array $workplaceDetails;
    private array $roomKeys;
    private array $currentRoom;
    private bool $isAdmin;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();
        $staffStuff = Staff::all();
        $this->workplaceDetails = $staffStuff[1];
        $currentId = 0;
        $isCurrentEmployee = false;

        if($_SESSION['adminStatus'] == true)
            $isCurrentEmployee = true;
        else
        {
            foreach($staffStuff[0] as $item) {
                if ($_SESSION['currentId'] == intval($_GET['employee_id'])) {
                    $isCurrentEmployee = true;
                }
            }
        }

        if($_SESSION['adminStatus'] == false && $isCurrentEmployee == false)
        {
            //header('Location: ../logoff.php');
            header('HTTP/1.0 403 Forbidden');
            die;
        }

        $this->isAdmin = $_SESSION['adminStatus'];

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $employeeId = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);
                if (!$employeeId)
                    throw new BadRequestException();

                if($employeeId != $_GET['employee_id'])
                {
                    header('Location: ../logoff.php');
                }

                $this->employee = Staff::findByID($employeeId);
                if (!$this->employee)
                    throw new NotFoundException();

                $pdo = PDOProvider::get();
                $query3 = "SELECT `room` FROM `key` WHERE `employee` = " . $employeeId;
                $stmt3 = $pdo->query($query3);
                $this->roomKeys = $stmt3->fetchAll(PDO::FETCH_ASSOC);

                foreach($this->roomKeys as $item){
                    $workplace = array_search($item['room'], array_column($this->workplaceDetails, 'room_id'));
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

                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                //načíst data
                $this->employee = Staff::readPost();
                $passwordCheck = false;

                if($this->employee->password)
                {
                    $passwordCheck = true;
                }


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
                if ($this->employee->validate($this->errors) && ($_SESSION['adminStatus'] || $isCurrentEmployee))
                {
                    //zpracovat
                    $result = $this->employee->update($passwordCheck);
                    //přesměrovat
                    $this->redirect(self::ACTION_UPDATE, $result);
                }
                else if($_SESSION['adminStatus'] == false && $isCurrentEmployee == false)
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