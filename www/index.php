<?php
require_once "../bootstrap/bootstrap.php";
session_start();

if($_SESSION['loggedIn'] == null)
{
    $_SESSION['loggedIn'] = false;
}

class IndexPage extends Page
{
    public string $title = "Prohlížeč databáze";

    protected function pageBody(): string
    {
        return "";
    }
}
$page = new IndexPage();
$page->checkLogin($_SESSION['loggedIn']);

