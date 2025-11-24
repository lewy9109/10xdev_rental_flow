<?php

declare(strict_types=1);

namespace App\Entity\Property;

enum PropertyType: string
{
    case PREMISES = 'premises';
    case APARTMENT = 'apartment';
    case HOUSE = 'house';
    case OTHER = 'other';
}
