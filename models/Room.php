<?php
class Room
{
    public ?int $room_id;
    public ?string $name;
    public ?string $phone;
    public ?string $no;

    private static string $table = 'room';

    public function __construct(array $rawData = [])
    {
        $this->hydrate($rawData);
    }


    /**
     * @param $sort
     * @return Room[]
     */
    public static function all(array $sort = []) : array
    {
        $pdo = PDOProvider::get();

        $query = "SELECT * FROM `" . self::$table . "` " . self::sortSQL($sort);
        $stmt = $pdo->query($query);

        $result = [];
        while($room = $stmt->fetch(PDO::FETCH_ASSOC))
            $result[] = new Room($room);

        return $result;
    }

//    public static function find($conditions, $sort){
//    }

    public static function findByID(int $id) : Room|null
    {
        $pdo = PDOProvider::get();
        $query = "SELECT * FROM `" . self::$table . "` WHERE `room_id` = $id";
        $stmt = $pdo->query($query);

        if ($stmt->rowCount() < 1)
            return null;

        return new Room($stmt->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @param array $rawData
     * @return void
     */
    private function hydrate(array $rawData): void
    {
        if (array_key_exists('room_id', $rawData)) {
            $this->room_id = $rawData['room_id'];
        }
        if (array_key_exists('name', $rawData)) {
            $this->name = $rawData['name'];
        }
        if (array_key_exists('phone', $rawData)) {
            $this->phone = $rawData['phone'];
        }
        if (array_key_exists('no', $rawData)) {
            $this->no = $rawData['no'];
        }
    }

    private static function sortSQL(array $sort) : string
    {
        if (!$sort)
            return "";

        $sqlChunks = [];
        foreach ($sort as $column => $direction){
            $sqlChunks[] = "`$column` $direction";
        }
        return "ORDER BY " . implode(" ", $sqlChunks);
    }

    public static function readPost() : Room
    {
        $room = new Room();

        $room->room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $room->name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
        $room->no = filter_input(INPUT_POST, 'no', FILTER_DEFAULT);
        $room->phone = filter_input(INPUT_POST, 'phone', FILTER_DEFAULT);

        return $room;
    }

    public function validate(array &$errors = []) : bool
    {
        if (is_string($this->name))
            $this->name = trim($this->name);
        if (!$this->name)
            $errors['name'] = "Jméno nemůže být prázdné";

        if (is_string($this->no))
            $this->no = trim($this->no);
        if (!$this->no)
            $errors['no'] = "Číslo nemůže být prázdné";

        if (is_string($this->phone))
            $this->phone = trim($this->phone);
        if (!$this->phone)
            $this->phone = NULL;

        return count($errors) === 0;
    }

    public function insert() : bool
    {
        $query = "INSERT INTO `".self::$table."` (`name`, `no`, `phone`) VALUES (:name, :no, :phone);";
        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'name' => $this->name,
            'no' => $this->no,
            'phone' => $this->phone
        ]);

    }

    public function update() : bool
    {
        $query = "UPDATE `".self::$table."` SET `name` = :name, `no` = :no, `phone` = :phone WHERE `room_id`=:roomId;";
        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'roomId' => $this->room_id,
            'name' => $this->name,
            'no' => $this->no,
            'phone' => $this->phone
        ]);

    }



    public static function deleteById(int $roomId) : bool
    {
        $query = "DELETE FROM `".self::$table."` WHERE `room_id` = :roomId";

        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'roomId' => $roomId,
        ]);
    }

    public function delete() : bool
    {
        return static::deleteById($this->room_id);
    }
}