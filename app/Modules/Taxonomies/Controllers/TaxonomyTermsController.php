<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Controllers;

use Rominas\Taxonomies\Actions\CreateTaxonomyTermAction;
use Rominas\Taxonomies\Actions\DeleteTaxonomyTermAction;
use Rominas\Taxonomies\Actions\UpdateTaxonomyTermAction;
use Rominas\Taxonomies\Factories\TaxonomyTermDataFactory;
use Rominas\Taxonomies\Model\TaxonomyTerm;
use Rominas\Taxonomies\QueryBuilders\TaxonomyTermQueryBuilder;
use Rominas\Taxonomies\Requests\CreateTaxonomyTermRequest;
use Rominas\Taxonomies\Requests\UpdateTaxonomyTermRequest;
use Rominas\Taxonomies\Resources\TaxonomyTermResource;
use Rominas\Users\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use function response;

class TaxonomyTermsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $query = TaxonomyTerm::query()->assignableByUser($user);

        if (! $request->has('showHidden')) {
            $query = $query->excludeHidden();
        }

        if ($request->has('ids')) {
            return TaxonomyTermResource::collection(
                $query->findMany((array) $request->query('ids')),
            );
        }

        if ($request->has('taxonomyId')) {
            $query = $query->filterByTaxonomyId((int) $request->query('taxonomyId'));
        }

        if ($request->has('parentsOnly')) {
            $query = $query->getOnlyParents();
        }

        return TaxonomyTermResource::collection(
            $query
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', TaxonomyTermQueryBuilder::PER_PAGE)),
        );
    }

    public function show(TaxonomyTerm $taxonomyTerm): TaxonomyTermResource
    {
        return new TaxonomyTermResource($taxonomyTerm);
    }

    public function create(
        CreateTaxonomyTermRequest $request,
        CreateTaxonomyTermAction $createTaxonomyTermAction,
    ): JsonResponse {
        $taxonomyTerm = $createTaxonomyTermAction->execute(
            TaxonomyTermDataFactory::fromRequest($request),
        );

        return (new TaxonomyTermResource($taxonomyTerm))->response()->setStatusCode(201);
    }

    public function update(
        TaxonomyTerm $taxonomyTerm,
        UpdateTaxonomyTermRequest $request,
        UpdateTaxonomyTermAction $updateTaxonomyTermAction,
    ): TaxonomyTermResource {
        $taxonomyTerm = $updateTaxonomyTermAction->execute(
            $taxonomyTerm,
            TaxonomyTermDataFactory::fromRequest($request),
        );

        return new TaxonomyTermResource($taxonomyTerm);
    }

    public function delete(
        TaxonomyTerm $taxonomyTerm,
        DeleteTaxonomyTermAction $deleteTaxonomyTermAction,
    ): JsonResponse {
        $result = $deleteTaxonomyTermAction->execute($taxonomyTerm);

        return response()->json(['success' => $result]);
    }
}
