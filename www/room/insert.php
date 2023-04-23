<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class RoomInsertPage extends CRUDPage
{
    public string $title = "Přidat novou místnost";
    protected int $state;
    private Room $room;
    private array $errors;
    public bool $duplicityPhoneCheck;
    public bool $duplicityNumberCheck;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();
        $this->duplicityPhoneCheck = true;
        $this->duplicityNumberCheck = true;

        if($_SESSION['adminStatus'] == false)
        {
            //header('Location: ../logoff.php');
            header('HTTP/1.0 403 Forbidden');
            die;
        }

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $this->room = new Room();
                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                //načíst data
                $this->room = Room::readPost();
                $allRooms = Room::all();
                $this->errors = [];

                foreach($allRooms as $item){
                    $counter = 0;
                    if($item->phone == $this->room->phone && $item->phone != "")
                    {
                        $this->duplicityPhoneCheck = false;
                        $this->errors['phone'] = "Tento telefon už existuje";
                        break;
                    }
                    $counter++;
               }

                foreach($allRooms as $item){
                    if($item->no == $this->room->no)
                    {
                        $this->duplicityNumberCheck = false;
                        $this->errors['no'] = "Číslo už existuje";
                        break;
                    }
                }

                //zkontrolovat data
                if ($this->room->validate($this->errors) && $this->duplicityPhoneCheck && $this->duplicityNumberCheck && $_SESSION['adminStatus'])
                {
                    //zpracovat
                    $result = $this->room->insert();
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