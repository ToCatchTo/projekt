<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class StaffListPage extends CRUDPage
{
    public string $title = "Seznam zaměstnanců";

    protected function pageBody(): string
    {
        $query = "SELECT admin FROM `employee` WHERE `login` = :username";
        $stmt = PDOProvider::get()->prepare($query);
        $stmt->execute(['username' => $_SESSION['username']]);
        $result = $stmt->fetchAll();

        if (isset($result[0]) && isset($result[0]->admin)) {
            $resultAdminCheck = ($result[0]->admin == 1);
        } else {
            $resultAdminCheck = false;
        }

        $html = $this->alert();

        $employees = Staff::all();
        $currentId = 0;

        foreach($employees[0] as $item){
            if($item->login == $_SESSION['username'])
            {
                $currentId = $item->employee_id;
                $item->loggedInEmployee = true;
            }
        }

        $html .= MustacheProvider::get()->render("employee_list", ["employees" => $employees[0], "adminState" => $resultAdminCheck]);
        //vyrenderuju

        return $html;
    }

    private function alert() : string
    {
        $action = filter_input(INPUT_GET, 'action');
        if (!$action)
            return "";

        $success = filter_input(INPUT_GET, 'success', FILTER_VALIDATE_INT);
        $data = [];

        switch ($action)
        {
            case self::ACTION_INSERT:
                if ($success === 1)
                {
                    $data['message'] = 'Zaměstnanec byl přidán';
                    $data['alertType'] = 'success';
                }
                else
                {
                    $data['message'] = 'Chyba při založení místnosti';
                    $data['alertType'] = 'danger';
                }
                break;

            case self::ACTION_DELETE:
                if ($success === 1)
                {
                    $data['message'] = 'Zaměstnanec byl smazán';
                    $data['alertType'] = 'success';
                }
                else
                {
                    $data['message'] = 'Chyba při odebírání zaměstnance';
                    $data['alertType'] = 'danger';
                }
                break;
        }

        return MustacheProvider::get()->render("alert", $data);
    }
}

$page = new StaffListPage();
$page->checkLogin($_SESSION['loggedIn']);