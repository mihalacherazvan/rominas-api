<?php

declare(strict_types=1);

namespace Rominas\Catalog\Album\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Catalog\Album\Actions\CreateAlbumAction;
use Rominas\Catalog\Album\Actions\DeleteAlbumAction;
use Rominas\Catalog\Album\Actions\UpdateAlbumAction;
use Rominas\Catalog\Album\Factories\AlbumDataFactory;
use Rominas\Catalog\Album\Model\Album;
use Rominas\Catalog\Album\QueryBuilders\AlbumQueryBuilder;
use Rominas\Catalog\Album\Requests\CreateAlbumRequest;
use Rominas\Catalog\Album\Requests\UpdateAlbumRequest;
use Rominas\Catalog\Album\Resources\AlbumResource;
use Rominas\Users\Model\User;

use function response;

class AlbumsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return AlbumResource::collection(
            Album::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'name'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', AlbumQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Album $album): AlbumResource
    {
        return new AlbumResource($album);
    }

    public function create(CreateAlbumRequest $request, CreateAlbumAction $action): JsonResponse
    {
        $album = $action->execute(AlbumDataFactory::fromRequest($request));

        return (new AlbumResource($album))->response()->setStatusCode(201);
    }

    public function update(
        Album $album,
        UpdateAlbumRequest $request,
        UpdateAlbumAction $action,
    ): AlbumResource {
        $album = $action->execute($album, AlbumDataFactory::fromRequest($request));

        return new AlbumResource($album);
    }

    public function delete(Album $album, DeleteAlbumAction $action): JsonResponse
    {
        $result = $action->execute($album);

        return response()->json(['success' => $result]);
    }
}
