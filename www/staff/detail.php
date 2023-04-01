<?php
require_once "../../bootstrap/bootstrap.php";
session_start();

class EmployeeDetailPage extends Page
{
    //public string $title = "Detail zaměstnance";
    private $room;
    private $employees;

    protected function prepareData(): void
    {
        parent::prepareData();

        //na koho se ptá (příp chyba)
        $employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);

        if (!$employee_id) {
            throw new BadRequestException();
        }

        //vytáhnu zaměstanace podle ID
        $this->employees = Staff::findByID($employee_id);

        //mám ho? (příp chyba)
        if (!$this->employees){
            throw new NotFoundException();
        }

        $this->title = htmlspecialchars( "Detail {$this->employees->name} {$this->employees->surname}" );

        //získám lidi
        //$query = "SELECT employee.`employee_id`, employee.`name`, employee.`surname`, employee.`wage`, employee.`job`, employee.`login`, employee.`room`, room.`name` FROM `employee` JOIN `room` WHERE `employee_id` = :employeeId";
        //$query = "SELECT room.`name` AS `workplace`, room.room_id FROM `employee` JOIN `room` WHERE `employee_id` = :employeeId ORDER BY `workplace`";
        $query = "SELECT r.`name` as `workplace`, r.`room_id`, e.`employee_id` FROM `employee` e JOIN `key` k ON e.`employee_id` = k.`employee` JOIN `room` r ON r.`room_id` = k.`room` WHERE e.`employee_id` = :employeeId";
        $stmt = PDOProvider::get()->prepare($query);
        $stmt->execute(['employeeId' => $employee_id]);
        $this->room = $stmt->fetchAll();
    }

    protected function pageBody(): string
    {
        //ukážu místnost
        return MustacheProvider::get()->render("employee_detail", ["employees" => $this->employees, 'room' => $this->room]);
    }
}

$page = new EmployeeDetailPage();
$page->checkLogin($_SESSION['loggedIn']);