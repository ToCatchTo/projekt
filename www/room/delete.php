<?php
require_once "../../bootstrap/bootstrap.php";
session_start();


class RoomDeletePage extends CRUDPage
{

    protected function prepareData(): void
    {
        parent::prepareData();
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        if (!$roomId)
            throw new BadRequestException();

        $employees = Staff::all();
        $_SESSION['roomOccupied'] = false;

        foreach($employees[0] as $item){
            if($item->room == $roomId)
            {
                $_SESSION['roomOccupied'] = true;
            }
        }

        if($_SESSION['roomOccupied'])
        {
            header("Location: list.php");
            exit;
        }

        $pdo = PDOProvider::get();
        $query = 'SELECT `room` FROM `key`';
        $stmt = $pdo->query($query);
        $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($keys as $item){
            if($item['room'] == $roomId)
            {
                $query2 = 'DELETE FROM `key` WHERE `room` = ' . $roomId;
                $preparedStmt2 = $pdo->prepare($query2);
                $preparedStmt2->execute();
                break;
            }
        }

        $result = Room::deleteById($roomId);
        $this->redirect(self::ACTION_DELETE, $result);
    }

    protected function pageBody(): string
    {
        return "";
    }
}

$page = new RoomDeletePage();
$page->checkLogin($_SESSION['loggedIn']);