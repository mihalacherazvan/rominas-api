<?php

declare(strict_types=1);

namespace Rominas\Catalog\Artist\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Catalog\Artist\Actions\CreateArtistAction;
use Rominas\Catalog\Artist\Actions\DeleteArtistAction;
use Rominas\Catalog\Artist\Actions\UpdateArtistAction;
use Rominas\Catalog\Artist\Factories\ArtistDataFactory;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Catalog\Artist\QueryBuilders\ArtistQueryBuilder;
use Rominas\Catalog\Artist\Requests\CreateArtistRequest;
use Rominas\Catalog\Artist\Requests\UpdateArtistRequest;
use Rominas\Catalog\Artist\Resources\ArtistResource;
use Rominas\Users\Model\User;

use function response;

class ArtistsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return ArtistResource::collection(
            Artist::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'name'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', ArtistQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Artist $artist): ArtistResource
    {
        return new ArtistResource($artist);
    }

    public function create(CreateArtistRequest $request, CreateArtistAction $action): JsonResponse
    {
        $artist = $action->execute(ArtistDataFactory::fromRequest($request));

        return (new ArtistResource($artist))->response()->setStatusCode(201);
    }

    public function update(
        Artist $artist,
        UpdateArtistRequest $request,
        UpdateArtistAction $action,
    ): ArtistResource {
        $artist = $action->execute($artist, ArtistDataFactory::fromRequest($request));

        return new ArtistResource($artist);
    }

    public function delete(Artist $artist, DeleteArtistAction $action): JsonResponse
    {
        $result = $action->execute($artist);

        return response()->json(['success' => $result]);
    }
}
