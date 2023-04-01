<?php

class ExceptionPage extends Page
{
    protected Exception $exception;

    /**
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
        $this->title = "Error";
    }

    protected function HTTPHeaders(): void
    {
        parent::HTTPHeaders();
        http_response_code($this->exception->getCode());
    }

    protected function pageBody(): string
    {
        $data = [
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage()
        ];

        return MustacheProvider::get()->render('exception', $data);
    }
}