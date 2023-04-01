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