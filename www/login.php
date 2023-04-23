<?php
require_once "../bootstrap/bootstrap.php";
session_start();

class LoginForm extends Page
{
    public string $title = "Přihlášení";

    protected function pageBody(): string
    {
        // Check if the form has been submitted
        if (isset($_POST['login'])) {
            // Get the username and password from the form data
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Get the PDO instance from PDOProvider
            $pdo = PDOProvider::get();

            // Validate the user's credentials
            $stmt = $pdo->prepare('SELECT * FROM `employee` WHERE login = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Store the user's username in a session variable
                $_SESSION['username'] = $username;
                $_SESSION['loggedIn'] = true;
                $_SESSION['adminStatus'] = false;
                $_SESSION['currentId'] = false;
                $employees = Staff::all();

                foreach($employees[0] as $item) {
                    if ($item->login == $_SESSION['username']) {
                        if($item->admin)
                            $_SESSION['adminStatus'] = true;
                        $_SESSION['currentId'] = $item->employee_id;
                    }
                }

                // Redirect to the home page
                header('Location: index.php');
                exit();
            } else {
                // Display an error message
                echo "Invalid username or password.";
            }
        }

        return MustacheProvider::get()->render("login_form", ["title" => $this->title]);
    }
}

$page = new LoginForm();
$page->render();
