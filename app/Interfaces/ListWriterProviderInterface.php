<?php

namespace App\Interfaces;

interface ListWriterProviderInterface
{

    function setProvider(ListModelInterface $provider): void;

    function getMessage(array $data, &$errors = []): array|null;

    function validateRules(): array;

    function prepare($validator, $guid, $date): array;

    function channel(): string;

    function user(): int;

    function getGuid(): string;

}
