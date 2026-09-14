<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Class weights
    |--------------------------------------------------------------------------
    |
    | The academy-vs-public result weighting is NOT global config — it lives on
    | each edition (`editions.academy_vote_weight` / `public_vote_weight`,
    | defaulting to the client-confirmed 60/40) so an edition can be reweighted
    | independently. Scoring reads it per edition; see the Edition model.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Display precision
    |--------------------------------------------------------------------------
    |
    | Decimal places kept when rounding the normalized shares and final score
    | for storage/presentation. Ranking never uses these rounded values — it is
    | decided on an exact integer key — so this is purely cosmetic.
    |
    */

    'precision' => 6,

];
