<?php

require_once "../bootstrap/bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $job = $_POST["job"];
    $wage = $_POST["wage"];
    $room = $_POST["room"];
    $login = $_POST["login"];
    $password = $_POST["password"];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = PDOProvider::get()->prepare("
        INSERT INTO employee (name, surname, job, wage, room, login, password)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $surname, $job, $wage, $room, $login, $hashed_password]);

    // Redirect to login page
    header("Location: login.php");
    exit;
}

//TODO vyjimky napr.: jestli je username stejne, atd

// If the request method is not POST, display the registration form
session_start();

class RegistrationForm extends Page
{
    public string $title = "Registrace";

    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("registration_form", ["title" => $this->title]);
    }
}

$page = new RegistrationForm();
$page->render();
