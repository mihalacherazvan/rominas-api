<?php

declare(strict_types=1);

namespace Rominas\Catalog\Song\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Catalog\Song\Actions\CreateSongAction;
use Rominas\Catalog\Song\Actions\DeleteSongAction;
use Rominas\Catalog\Song\Actions\UpdateSongAction;
use Rominas\Catalog\Song\Factories\SongDataFactory;
use Rominas\Catalog\Song\Model\Song;
use Rominas\Catalog\Song\QueryBuilders\SongQueryBuilder;
use Rominas\Catalog\Song\Requests\CreateSongRequest;
use Rominas\Catalog\Song\Requests\UpdateSongRequest;
use Rominas\Catalog\Song\Resources\SongResource;
use Rominas\Users\Model\User;

use function response;

class SongsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return SongResource::collection(
            Song::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'name'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', SongQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Song $song): SongResource
    {
        return new SongResource($song);
    }

    public function create(CreateSongRequest $request, CreateSongAction $action): JsonResponse
    {
        $song = $action->execute(SongDataFactory::fromRequest($request));

        return (new SongResource($song))->response()->setStatusCode(201);
    }

    public function update(
        Song $song,
        UpdateSongRequest $request,
        UpdateSongAction $action,
    ): SongResource {
        $song = $action->execute($song, SongDataFactory::fromRequest($request));

        return new SongResource($song);
    }

    public function delete(Song $song, DeleteSongAction $action): JsonResponse
    {
        $result = $action->execute($song);

        return response()->json(['success' => $result]);
    }
}
