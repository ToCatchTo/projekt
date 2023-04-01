<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class RoomInsertPage extends CRUDPage
{
    public string $title = "Upravit místnost";
    protected int $state;
    private Room $room;
    private array $errors;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
                if (!$roomId)
                    throw new BadRequestException();

                $this->room = Room::findByID($roomId);
                if (!$this->room)
                    throw new NotFoundException();

                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                //načíst data
                $this->room = Room::readPost();
                //zkontrolovat data
                $this->errors = [];
                if ($this->room->validate($this->errors))
                {
                    //zpracovat
                    $result = $this->room->update();
                    //přesměrovat
                    $this->redirect(self::ACTION_UPDATE, $result);
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
        return MustacheProvider::get()->render("room_form",
            [
                'room' => $this->room,
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

$page = new RoomInsertPage();
$page->checkLogin($_SESSION['loggedIn']);