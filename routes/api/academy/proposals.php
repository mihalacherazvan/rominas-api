<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Academy\MemberProposal\Controllers\MemberProposalsController;

// A member's own proposals for future academy members (guard `member`).
Route::get('/proposals', [MemberProposalsController::class, 'index'])->name('proposals.index');
Route::post('/proposals', [MemberProposalsController::class, 'create'])->name('proposals.create');
Route::delete('/proposals/{proposal}', [MemberProposalsController::class, 'withdraw'])->name('proposals.withdraw');
