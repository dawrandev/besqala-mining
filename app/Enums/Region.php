<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Districts and cities of the Republic of Karakalpakstan where the
 * special mining zone operates.
 */
enum Region: string
{
    case NukusCity = 'nukus_city';
    case NukusDistrict = 'nukus_district';
    case Qongirot = 'qongirot';
    case Qoraozak = 'qoraozak';
    case Chimboy = 'chimboy';
    case Ellikqala = 'ellikqala';
    case Beruniy = 'beruniy';
    case Amudaryo = 'amudaryo';
    case Kegeyli = 'kegeyli';
    case Shumanay = 'shumanay';
    case Taxiatosh = 'taxiatosh';
    case Moynoq = 'moynoq';
    case Taxtakopir = 'taxtakopir';
    case Tortkol = 'tortkol';
    case Qanlikol = 'qanlikol';

    public function label(): string
    {
        return match ($this) {
            self::NukusCity => 'Nukus shahri',
            self::NukusDistrict => 'Nukus tumani',
            self::Qongirot => 'Qo‘ng‘irot tumani',
            self::Qoraozak => 'Qorao‘zak tumani',
            self::Chimboy => 'Chimboy tumani',
            self::Ellikqala => 'Ellikqal’a tumani',
            self::Beruniy => 'Beruniy tumani',
            self::Amudaryo => 'Amudaryo tumani',
            self::Kegeyli => 'Kegeyli tumani',
            self::Shumanay => 'Shumanay tumani',
            self::Taxiatosh => 'Taxiatosh tumani',
            self::Moynoq => 'Mo‘ynoq tumani',
            self::Taxtakopir => 'Taxtako‘pir tumani',
            self::Tortkol => 'To‘rtko‘l tumani',
            self::Qanlikol => 'Qanliko‘l tumani',
        };
    }

    /**
     * Options for a select control: value => label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
