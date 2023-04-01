<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class RoomDetailPage extends Page
{
//    public string $title = "Seznam místností";
    private $room;
    private $employees;

    protected function prepareData(): void
    {
        parent::prepareData();

        //na koho se ptá (příp chyba)
        $room_id = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);

        if (!$room_id) {
            throw new BadRequestException();
        }

        //vytáhnu místnost
        $this->room = Room::findByID($room_id);

        //mám ho? (příp chyba)
        if (!$this->room){
            throw new NotFoundException();
        }

        $this->title = htmlspecialchars( "Místnost {$this->room->no} ({$this->room->name})" );

        //získám lidi
        $query = "SELECT `employee_id`, `name`, `surname` FROM `employee` WHERE `room` = :roomId ORDER BY `surname`, `name`";
        $stmt = PDOProvider::get()->prepare($query);
        $stmt->execute(['roomId' => $room_id]);
        $this->employees = $stmt->fetchAll();
    }

    protected function pageBody(): string
    {
        //ukážu místnost
        return MustacheProvider::get()->render("room_detail", ["room" => $this->room, 'employees' => $this->employees]);
    }
}

$page = new RoomDetailPage();
$page->checkLogin($_SESSION['loggedIn']);