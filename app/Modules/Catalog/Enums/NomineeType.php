<?php

declare(strict_types=1);

namespace Rominas\Catalog\Enums;

use Illuminate\Database\Eloquent\Model;
use Rominas\Catalog\Album\Model\Album;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Band\Model\Band;
use Rominas\Catalog\Song\Model\Song;
use Rominas\Catalog\Venue\Model\Venue;

/**
 * The nominatable entity type an award category accepts. Backed by stable slugs
 * (not class FQNs) so DB rows and API payloads stay decoupled from PHP namespaces;
 * modelClass() resolves the slug to its Eloquent model, morph-map style.
 */
enum NomineeType: string
{
    case Artist = 'artist';
    case Band = 'band';
    case Venue = 'venue';
    case Song = 'song';
    case Album = 'album';

    /**
     * @return class-string<Model>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Artist => Artist::class,
            self::Band => Band::class,
            self::Venue => Venue::class,
            self::Song => Song::class,
            self::Album => Album::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Artist => 'Artist',
            self::Band => 'Band',
            self::Venue => 'Venue',
            self::Song => 'Song',
            self::Album => 'Album',
        };
    }
}
