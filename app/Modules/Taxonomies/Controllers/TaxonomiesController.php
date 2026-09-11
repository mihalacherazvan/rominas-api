<?php

declare(strict_types=1);

namespace Rominas\Taxonomies\Controllers;

use Rominas\Taxonomies\Actions\CreateTaxonomyAction;
use Rominas\Taxonomies\Actions\DeleteTaxonomyAction;
use Rominas\Taxonomies\Actions\UpdateTaxonomyAction;
use Rominas\Taxonomies\Factories\TaxonomyDataFactory;
use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Taxonomies\QueryBuilders\TaxonomyQueryBuilder;
use Rominas\Taxonomies\Requests\CreateTaxonomyRequest;
use Rominas\Taxonomies\Requests\UpdateTaxonomyRequest;
use Rominas\Taxonomies\Resources\TaxonomyResource;
use Rominas\Users\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use function response;

class TaxonomiesController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return TaxonomyResource::collection(
            Taxonomy::query()
                ->visibleToUser($user)
                ->search($request->query('search'))
                ->orderBy(
                    $request->query('orderBy'),
                    $request->query('orderDir', 'asc'),
                )
                ->paginate($request->query('perPage', TaxonomyQueryBuilder::PER_PAGE)),
        );
    }

    public function show(Taxonomy $taxonomy): TaxonomyResource
    {
        return new TaxonomyResource($taxonomy);
    }

    public function create(CreateTaxonomyRequest $request, CreateTaxonomyAction $createTaxonomyAction): JsonResponse
    {
        $taxonomy = $createTaxonomyAction->execute(
            TaxonomyDataFactory::fromRequest($request),
        );

        return (new TaxonomyResource($taxonomy))->response()->setStatusCode(201);
    }

    public function update(
        Taxonomy $taxonomy,
        UpdateTaxonomyRequest $request,
        UpdateTaxonomyAction $updateTaxonomyAction,
    ): TaxonomyResource {
        $taxonomy = $updateTaxonomyAction->execute(
            $taxonomy,
            TaxonomyDataFactory::fromRequest($request),
        );

        return new TaxonomyResource($taxonomy);
    }

    public function delete(Taxonomy $taxonomy, DeleteTaxonomyAction $deleteTaxonomyAction): JsonResponse
    {
        $result = $deleteTaxonomyAction->execute($taxonomy);

        return response()->json(['success' => $result]);
    }
}
