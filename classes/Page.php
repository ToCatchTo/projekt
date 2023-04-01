<?php

abstract class Page
{
    public string $title;

    protected function prepareData() : void {

    }

    protected function HTTPHeaders() : void {

    }

    protected function HTMLHead() : string {
        return MustacheProvider::get()->render("html_head", ["title" => $this->title]);
    }

    protected function pageHeader() : string {
        if($this->title == "Registrace")
        {
            return "";
        }
        else if($this->title == "Přihlášení")
        {
            return "";
        }
        return MustacheProvider::get()->render("page_header", []);
    }

    protected abstract function pageBody() : string;

    public function checkLogin(bool $loggedIn): void {
        if (!$loggedIn && !isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
            if ($this->title == "Prohlížeč databáze") {
                header('Location: login.php');
                exit;
            }
            header('Location: ../login.php');
            exit;
        } else {
            $this->render();
        }
    }


    public function render() : void {
        try {
            $this->prepareData();

            //pošle http hlavičky
            $this->HTTPHeaders();

            $pageData = [];
            //získá hlavičky
            $pageData["htmlHead"] = $this->HTMLHead();

            //získá záhlaví
            $pageData["pageHeader"] = $this->pageHeader();

            //získá tělo stránky
            $pageData["pageBody"] = $this->pageBody();

            //předá šabloně stránky data pro vykreslení
            echo MustacheProvider::get()->render("page", $pageData);
        }

        catch (BaseException $e) {
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }

        catch (Exception $e) {
            if (AppConfig::get('debug'))
                throw $e;

            $e = new BaseException();
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }
    }
}