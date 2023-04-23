<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class RoomListPage extends CRUDPage
{
    public string $title = "Seznam místností";

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
        if($_SESSION['roomOccupied'])
        {
            echo '<script>alert("Místnost je pro někoho domovská, takže nemůže být smazána")</script>';
            $_SESSION['roomOccupied'] = false;
        }

        $html = $this->alert();

        //získám data o místnostech
        $rooms = Room::all();

        $html .= MustacheProvider::get()->render("room_list", ["rooms" => $rooms, "adminState" => $resultAdminCheck]);
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
                    $data['message'] = 'Mistnost byla založena';
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
                    $data['message'] = 'Mistnost byla smazána';
                    $data['alertType'] = 'success';
                }
                else
                {
                    $data['message'] = 'Chyba při mazání místnosti';
                    $data['alertType'] = 'danger';
                }
                break;
        }


        return MustacheProvider::get()->render("alert", $data);
    }
}

$page = new RoomListPage();
$page->checkLogin($_SESSION['loggedIn']);