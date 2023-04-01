<?php

class Staff
{
    public ?int $employee_id;
    public ?string $name;
    public ?string $surname;
    public ?string $job;
    public ?int $wage;
    public ?string $login;
    //public ?int $room;
    private ?string $password;
    public ?bool $admin;

    private static string $table = 'employee';

    public function __construct(array $rawData = [])
    {
        $this->hydrate($rawData);
    }


    /**
     * @param $sort
     * @return Employee[]
     */
    public static function all(array $sort = []): array
    {
        $pdo = PDOProvider::get();

        //$query = "SELECT employee.`name`, room.`name` AS `workplace`, employee.`surname`, employee.`job`, room.`phone`, employee.`employee_id` FROM `" . self::$table . "` JOIN `room` ON room.`room_id` = employee.`room` " . self::sortSQL($sort);
        $query = 'SELECT employee.`name`, employee.`surname`, employee.`job`, room.`phone`, employee.`employee_id`, employee.`wage`, room.`room_id`, employee.`room`, room.`name` AS workplace, employee.`password`, employee.`admin` FROM `employee` JOIN `room` ON room.`room_id` = employee.`room`';
        $stmt = $pdo->query($query);
        $query2 = 'SELECT room.`room_id`, room.`name` FROM `room`';
        $stmt2 = $pdo->query($query2);

        $workplaceInfo = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        while ($employee = $stmt->fetch(PDO::FETCH_ASSOC))
            $result[] = new Staff($employee);

        return array($result, $workplaceInfo);
    }

    public static function findByID(int $id) : Staff|null
    {
        $pdo = PDOProvider::get();
        $query = "SELECT * FROM `" . self::$table . "` WHERE `employee_id` = $id";
        $stmt = $pdo->query($query);

        if ($stmt->rowCount() < 1)
            return null;

        return new Staff($stmt->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @param array $rawData
     * @return void
     */
    private function hydrate(array $rawData): void
    {
        if (array_key_exists('employee_id', $rawData)) {
            $this->employee_id = $rawData['employee_id'];
        }
        if (array_key_exists('name', $rawData)) {
            $this->name = $rawData['name'];
        }
        if (array_key_exists('surname', $rawData)) {
            $this->surname = $rawData['surname'];
        }
        if (array_key_exists('job', $rawData)) {
            $this->job = $rawData['job'];
        }
        if (array_key_exists('wage', $rawData)) {
            $this->wage = $rawData['wage'];
        }
        if (array_key_exists('login', $rawData)) {
            $this->login = $rawData['login'];
        }
        if (array_key_exists('password', $rawData)) {
            $this->password = $rawData['password'];
        }
//        if (array_key_exists('room', $rawData)) {
//            $this->room = $rawData['room'];
//        }
        if (array_key_exists('admin', $rawData)) {
            if($rawData['admin'] == 1)
            {
                $this->admin = true;
            }
            else
            {
                $this->admin = false;
            }
        }
    }

    private static function sortSQL(array $sort): string
    {
        if (!$sort)
            return "";

        $sqlChunks = [];
        foreach ($sort as $column => $direction) {
            $sqlChunks[] = "`$column` $direction";
        }
        return "ORDER BY " . implode(" ", $sqlChunks);
    }

    public static function readPost(): Staff
    {
        $employee = new Staff();

        $employee->employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $employee->name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
        $employee->surname = filter_input(INPUT_POST, 'surname', FILTER_DEFAULT);
        $employee->job = filter_input(INPUT_POST, 'job', FILTER_DEFAULT);
        $employee->wage = filter_input(INPUT_POST, 'wage', FILTER_DEFAULT);
        $employee->login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
        $employee->admin = filter_input(INPUT_POST, 'admin', FILTER_DEFAULT);
        $employee->password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
//        $employee->room = filter_input(INPUT_POST, 'room', FILTER_DEFAULT);

        return $employee;
    }
//
    public function validate(array &$errors = []): bool
    {
        if (is_string($this->job))
            $this->job = trim($this->job);
        if (!$this->job)
            $errors['job'] = "Prácovní pozice nemůže být prázdná";

        if (is_string($this->wage))
            $this->wage = trim($this->wage);
        if (!$this->wage)
            $errors['wage'] = "Plat nemůže být prázdný";

        if (is_string($this->name))
            $this->name = trim($this->name);
        if (!$this->name)
            $errors['name'] = "Jméno musí být vyplněné";

        if (is_string($this->surname))
            $this->surname = trim($this->surname);
        if (!$this->surname)
            $errors['surname'] = "Příjmení musí být vyplněné";

        return count($errors) === 0;
    }

    public function insert(): bool
    {
        $query = "INSERT INTO `" . self::$table . "` (`name`, `surname`, `job`, `wage`, `login`, `password`, `admin`) VALUES (:name, :surname, :job, :wage, :login, :password, :admin)";
        $pdo = PDOProvider::get();
        if(isset($_POST['admin']) == null)
        {
            $this->admin = 0;
        }
        else
        {
            $this->admin = 1;
        }

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'name' => $this->name,
            'surname' => $this->surname,
            'job' => $this->job,
            'wage' => $this->wage,
//            'room' => $this->room,
            'login' => $this->login,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
            'admin' => $this->admin
        ]);

    }

    public function update(): bool
    {
        $query = "UPDATE `" . self::$table . "` SET `name` = :name, `surname` = :surname, `job` = :job, `wage` = :wage, `login` = :login, `password` = :password, `admin` = :admin WHERE `employee_id`=:employeeId;";
        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        if(isset($_POST['admin']) == null)
        {
            $this->admin = 0;
        }
        else
        {
            $this->admin = 1;
        }

        return $stmt->execute([
            'employeeId' => $this->employee_id,
            'name' => $this->name,
            'surname' => $this->surname,
            'job' => $this->job,
            'wage' => $this->wage,
//            'room' => $this->room,
            'login' => $this->login,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
            'admin' => $this->admin
        ]);
    }


    public static function deleteById(int $employeeId): bool
    {
        $query = "DELETE FROM `" . self::$table . "` WHERE `employee_id` = :employeeId";

        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'employeeId' => $employeeId,
        ]);
    }

    public function delete(): bool
    {
        return static::deleteById($this->employee_id);

    }
}