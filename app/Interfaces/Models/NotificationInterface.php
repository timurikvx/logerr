<?php

namespace App\Interfaces\Models;

interface NotificationInterface
{

    public function getGuid(): string;

    public function getType(): string;

    public function getData();

    function getTo();

    function getTitle();

    function getText();

    function getFrom();

    function getCompleted(): bool;

    function setCompleted(bool $value): void;

    function getMissed(): bool;

    function setMissed(bool $value): void;

}
