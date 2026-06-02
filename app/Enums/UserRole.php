<?php

namespace App\Enums;

enum UserRole:string
{
    case DOCTOR = 'doctor';

    case PATIENT = 'patient';

    case ADMIN = 'admin';
}