<?php

abstract class CRUDPage extends Page
{
    const STATE_FORM_REQUEST = 0;
    const STATE_DATA_SENT = 1;

    const ACTION_INSERT = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;

    protected function redirect(int $action, bool $success) : void
    {
        header("Location: list.php?".http_build_query([
            'success' => $success ? 1 : 0,
            'action' => $action
        ]));
        exit;
    }
}