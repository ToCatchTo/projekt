<?php

require_once "../bootstrap/bootstrap.php";
session_start();

class RegistrationForm extends Page
{
    public function prepareData() : void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $job = $_POST["job"];
            $room = $_POST["room"];
            $login = $_POST["login"];
            $password = $_POST["password"];
            $passwordAgain = $_POST["passwordAgain"];
            $hashed_password = "";

            if (is_string($passwordAgain))
                $passwordAgain = trim($passwordAgain);
            if($passwordAgain != $password)
                $errors['passwordAgain'] = "Heslo musí být stejné jako první heslo";
            if(count($errors) === 0)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = PDOProvider::get()->prepare("
                INSERT INTO employee (name, surname, job, room, login, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $surname, $job, $room, $login, $hashed_password]);

            // Redirect to login page
            header("Location: login.php");
            exit;
        }
    }

    public string $title = "Registrace";

    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("registration_form", ["title" => $this->title]);
    }
}

$page = new RegistrationForm();
$page->render();
