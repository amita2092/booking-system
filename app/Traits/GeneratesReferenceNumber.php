<?php

namespace App\Traits;

trait GeneratesReferenceNumber
{
    public function generateReferenceNumber(): string
    {
        return sprintf(
            'APT-%s-%s',
            now()->format('Ymd'),
            strtoupper(substr(uniqid(), -6))
        );
    }
}