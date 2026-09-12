<?php

declare(strict_types=1);

namespace Rominas\Editions\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Editions\Actions\CreateEditionAction;
use Rominas\Editions\Actions\DeleteEditionAction;
use Rominas\Editions\Actions\TransitionEditionAction;
use Rominas\Editions\Actions\UpdateEditionAction;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Factories\EditionDataFactory;
use Rominas\Editions\Model\Edition;
use Rominas\Editions\QueryBuilders\EditionQueryBuilder;
use Rominas\Editions\Requests\CreateEditionRequest;
use Rominas\Editions\Requests\TransitionEditionRequest;
use Rominas\Editions\Requests\UpdateEditionRequest;
use Rominas\Editions\Resources\EditionResource;
use Rominas\Users\Model\User;

use function response;

class EditionsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return EditionResource::collection(
            Edition::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy', 'starts_at'),
                    $request->query('orderDir', 'desc'),
                )
                ->paginate($request->query('perPage', EditionQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Edition $edition): EditionResource
    {
        return new EditionResource($edition);
    }

    public function create(CreateEditionRequest $request, CreateEditionAction $action): JsonResponse
    {
        $edition = $action->execute(EditionDataFactory::fromRequest($request));

        return (new EditionResource($edition))->response()->setStatusCode(201);
    }

    public function update(Edition $edition, UpdateEditionRequest $request, UpdateEditionAction $action): EditionResource
    {
        $edition = $action->execute($edition, EditionDataFactory::fromRequest($request));

        return new EditionResource($edition);
    }

    public function transition(
        Edition $edition,
        TransitionEditionRequest $request,
        TransitionEditionAction $action,
    ): EditionResource {
        $edition = $action->execute($edition, EditionStatus::from($request->validated()['status']));

        return new EditionResource($edition);
    }

    public function delete(Edition $edition, DeleteEditionAction $action): JsonResponse
    {
        $result = $action->execute($edition);

        return response()->json(['success' => $result]);
    }
}
