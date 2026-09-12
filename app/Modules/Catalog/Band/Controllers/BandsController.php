<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Catalog\Band\Actions\CreateBandAction;
use Rominas\Catalog\Band\Actions\DeleteBandAction;
use Rominas\Catalog\Band\Actions\UpdateBandAction;
use Rominas\Catalog\Band\Factories\BandDataFactory;
use Rominas\Catalog\Band\Model\Band;
use Rominas\Catalog\Band\QueryBuilders\BandQueryBuilder;
use Rominas\Catalog\Band\Requests\CreateBandRequest;
use Rominas\Catalog\Band\Requests\UpdateBandRequest;
use Rominas\Catalog\Band\Resources\BandResource;
use Rominas\Users\Model\User;

use function response;

class BandsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return BandResource::collection(
            Band::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'name'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', BandQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Band $band): BandResource
    {
        return new BandResource($band);
    }

    public function create(CreateBandRequest $request, CreateBandAction $action): JsonResponse
    {
        $band = $action->execute(BandDataFactory::fromRequest($request));

        return (new BandResource($band))->response()->setStatusCode(201);
    }

    public function update(
        Band $band,
        UpdateBandRequest $request,
        UpdateBandAction $action,
    ): BandResource {
        $band = $action->execute($band, BandDataFactory::fromRequest($request));

        return new BandResource($band);
    }

    public function delete(Band $band, DeleteBandAction $action): JsonResponse
    {
        $result = $action->execute($band);

        return response()->json(['success' => $result]);
    }
}
