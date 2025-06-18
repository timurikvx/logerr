<?php

namespace App\Services\DTO;

class NotificationDTO
{
    public function __construct(
        public string $type,
        public int $to,
        public string $title,
        public string $text,
        public mixed $data,
        public string $url = ''
    )
    {

    }
}
