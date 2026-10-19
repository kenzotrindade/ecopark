<?php

declare(strict_types=1);

namespace App\Models;

class ChargeSession
{
    public function __construct(
        public ?int $id,
        public int $stationId,
        public string $userName,
        public int $kwhRequested,
        public ?string $createdAt = null
    ) {}
}
