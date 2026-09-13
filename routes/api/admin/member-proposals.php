<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Academy\MemberProposal\Controllers\AdminMemberProposalsController;
use Rominas\Academy\MemberProposal\Model\MemberProposal;

Route::get('/', [AdminMemberProposalsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . MemberProposal::class);

Route::get('/{memberProposal}', [AdminMemberProposalsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,memberProposal');

Route::post('/{memberProposal}/approve', [AdminMemberProposalsController::class, 'approve'])
    ->name('approve')
    ->middleware('can:update,memberProposal');

Route::post('/{memberProposal}/reject', [AdminMemberProposalsController::class, 'reject'])
    ->name('reject')
    ->middleware('can:update,memberProposal');
