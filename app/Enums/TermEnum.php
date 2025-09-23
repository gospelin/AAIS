<?php
// app/Enums/TermEnum.php
namespace App\Enums;

enum TermEnum: string
{
    case FIRST = 'First';
    case SECOND = 'Second';
    case THIRD = 'Third';

    public function label(): string
    {
        return match ($this) {
            self::FIRST => 'First Term',
            self::SECOND => 'Second Term',
            self::THIRD => 'Third Term',
        };
    }
}
