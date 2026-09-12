<?php

declare(strict_types=1);

namespace Rominas\Catalog\Venue\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Catalog\Venue\Actions\CreateVenueAction;
use Rominas\Catalog\Venue\Actions\DeleteVenueAction;
use Rominas\Catalog\Venue\Actions\UpdateVenueAction;
use Rominas\Catalog\Venue\Factories\VenueDataFactory;
use Rominas\Catalog\Venue\Model\Venue;
use Rominas\Catalog\Venue\QueryBuilders\VenueQueryBuilder;
use Rominas\Catalog\Venue\Requests\CreateVenueRequest;
use Rominas\Catalog\Venue\Requests\UpdateVenueRequest;
use Rominas\Catalog\Venue\Resources\VenueResource;
use Rominas\Users\Model\User;

use function response;

class VenuesController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return VenueResource::collection(
            Venue::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'name'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', VenueQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Venue $venue): VenueResource
    {
        return new VenueResource($venue);
    }

    public function create(CreateVenueRequest $request, CreateVenueAction $action): JsonResponse
    {
        $venue = $action->execute(VenueDataFactory::fromRequest($request));

        return (new VenueResource($venue))->response()->setStatusCode(201);
    }

    public function update(
        Venue $venue,
        UpdateVenueRequest $request,
        UpdateVenueAction $action,
    ): VenueResource {
        $venue = $action->execute($venue, VenueDataFactory::fromRequest($request));

        return new VenueResource($venue);
    }

    public function delete(Venue $venue, DeleteVenueAction $action): JsonResponse
    {
        $result = $action->execute($venue);

        return response()->json(['success' => $result]);
    }
}
