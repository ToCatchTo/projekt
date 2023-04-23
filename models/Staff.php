<?php

class Staff
{
    public ?int $employee_id;
    public ?string $name;
    public ?string $surname;
    public ?string $job;
    public ?string $wage;
    public ?string $login;
    public ?array $room_keys;
    public ?int $room;
    public ?string $password;
    private ?string $passwordAgain;
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
        $query = 'SELECT employee.`name`, employee.`surname`, employee.`job`, room.`phone`, employee.`employee_id`, employee.`wage`, room.`room_id`, employee.`room`, room.`name` AS workplace, employee.`password`, employee.`admin`, false AS `loggedInEmployee`, employee.`login` FROM `employee` JOIN `room` ON room.`room_id` = employee.`room`';
        $stmt = $pdo->query($query);
        $query2 = 'SELECT room.`room_id`, room.`name`, false AS `checked`, false AS `selected` FROM `room`';
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
//        if (array_key_exists('password', $rawData)) {
//            $this->password = $rawData['password'];
//        }
//        if (array_key_exists('passwordAgain', $rawData)) {
//            $this->passwordAgain = $rawData['passwordAgain'];
//        }
        if (array_key_exists('room', $rawData)) {
            $this->room = $rawData['room'];
        }
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
        $employee->passwordAgain = filter_input(INPUT_POST, 'passwordAgain', FILTER_DEFAULT);
        $employee->room_keys = $_POST['room_keys'];
        $employee->room = filter_input(INPUT_POST, 'room', FILTER_DEFAULT);

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
        if ($this->wage < 0)
            $errors['wage'] = "Plat nesmí být záporný";

        if (is_string($this->name))
            $this->name = trim($this->name);
        if (!$this->name)
            $errors['name'] = "Jméno musí být vyplněné";

        if (is_string($this->surname))
            $this->surname = trim($this->surname);
        if (!$this->surname)
            $errors['surname'] = "Příjmení musí být vyplněné";

        if (is_string($this->login))
            $this->login = trim($this->login);
        if (!$this->login)
            $errors['login'] = "Uživatelské jméno nemůže být prázdné";

        if (is_string($this->passwordAgain))
            $this->passwordAgain = trim($this->passwordAgain);
        if($this->passwordAgain != $this->password)
            $errors['passwordAgain'] = "Heslo musí být stejné jako první heslo";

        return count($errors) === 0;
    }

    public function insert(): bool
    {
        $query = "INSERT INTO `" . self::$table . "` (`name`, `surname`, `job`, `wage`, `room`, `login`, `password`, `admin`) VALUES (:name, :surname, :job, :wage, :room, :login, :password, :admin)";
        $query2 = "INSERT INTO `key` (`employee`, `room`) VALUES (:employee_id, :room_key)";
        $query3 = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee'";
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
        $stmt2 = $pdo->prepare($query2);
        $stmt3 = $pdo->query($query3);

        try {
            $result = $stmt->execute([
                'name' => $this->name,
                'surname' => $this->surname,
                'job' => $this->job,
                'wage' => $this->wage,
                'room' => $_POST['room'],
                'login' => $this->login,
                'password' => password_hash($this->password, PASSWORD_DEFAULT),
                'admin' => 0 //$this->admin
            ]);

        } catch (Exception $e){
            echo $e; exit;
        }

        $lastId = $pdo->lastInsertId();

        foreach($this->room_keys as $item){
            $stmt2->execute(['employee_id' => $lastId, 'room_key' => $item]);
        }
        return $result;

    }

    public function update(bool $passwordCheck): bool
    {
        if ($passwordCheck)
            $query = "UPDATE `" . self::$table . "` SET `name` = :name, `surname` = :surname, `job` = :job, `wage` = :wage, `room` = :room, `login` = :login, `password` = :password, `admin` = :admin WHERE `employee_id`= :employeeId;";
        else
            $query = "UPDATE `" . self::$table . "` SET `name` = :name, `surname` = :surname, `job` = :job, `wage` = :wage, `room` = :room, `login` = :login, `admin` = :admin WHERE `employee_id`= :employeeId;";
        $query2 = "DELETE FROM `key` WHERE `room` = :room_key AND `employee` = :employee_id";
        $query3 = "INSERT INTO `key` (`employee`, `room`) VALUES (:employee_id, :room_key)";
        $query4 = "SELECT `room` FROM `key` WHERE `employee` = " . $this->employee_id;
        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        $stmt2 = $pdo->prepare($query2);
        $stmt3 = $pdo->prepare($query3);
        $stmt4 = $pdo->query($query4);

        if(isset($_POST['admin']) == null)
        {
            $this->admin = 0;
        }
        else
        {
            $this->admin = 1;
        }

        $roomKeys = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        foreach($roomKeys as $item){
            $workplace = array_search($item['room'], $this->room_keys);
            if(!$workplace)
            {
                $stmt2->execute(['employee_id' => $this->employee_id, 'room_key' => $item['room']]);
            }
        }

        foreach($this->room_keys as $item){
            $workplace = array_search($item, array_column($roomKeys, 'room'));
            if(!$workplace)
            {
                $stmt3->execute(['employee_id' => $this->employee_id, 'room_key' => $item]);
            }
        }

        if ($passwordCheck)
            return $stmt->execute([
                'employeeId' => $this->employee_id,
                'name' => $this->name,
                'surname' => $this->surname,
                'job' => $this->job,
                'wage' => $this->wage,
                'room' => $this->room,
                'login' => $this->login,
                'password' => password_hash($this->password, PASSWORD_DEFAULT),
                'admin' => $this->admin
            ]);
        else
            return $stmt->execute([
                'employeeId' => $this->employee_id,
                'name' => $this->name,
                'surname' => $this->surname,
                'job' => $this->job,
                'wage' => $this->wage,
                'room' => $this->room,
                'login' => $this->login,
                'admin' => $this->admin
            ]);    }


    public static function deleteById(int $employeeId): bool
    {
        $query = "DELETE FROM `" . self::$table . "` WHERE `employee_id` = :employeeId";

        $pdo = PDOProvider::get();

        $query2 = 'DELETE FROM `key` WHERE `employee` = ' . $employeeId;
        $stmt2 = $pdo->prepare($query2);
        $stmt2->execute();

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