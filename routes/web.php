<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingsController;
use Illuminate\Support\Facades\Route;

// ─── Stripe webhook (pas de middleware auth ni CSRF) ──────────────────────────
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// ─── Orange Money callbacks (pas de middleware auth — appelé par Orange) ─────
Route::post('/orange-money/callback', [TransactionController::class, 'orangeCallback'])->name('orange.callback');
Route::get('/orange-money/callback',  [TransactionController::class, 'orangeCallback']);
Route::get('/orange-money/sandbox-success', [TransactionController::class, 'orangeReturn'])->name('orange.return');
Route::get('/orange-money/return',    [TransactionController::class, 'orangeReturn'])->name('orange.return.get');

// ─── Page de maintenance ───────────────────────────────────────────────────
Route::get('/maintenance', fn() => view('maintenance'))->name('maintenance');

// ─── Page d'accueil publique ───────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('home');

// ─── Authentification ──────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout',  [AuthController::class, 'logout']);

// ─── Pages utilisateur connecté ────────────────────────────────────────────
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard',        [DashboardController::class, 'userDashboard'])->name('dashboard');
    Route::get('/history',          [TransactionController::class, 'history'])->name('history');
    Route::get('/history/{id}/pdf',     [PdfController::class, 'invoice'])->name('history.pdf');
    Route::get('/history/{id}/preview', [PdfController::class, 'preview'])->name('history.preview');
    Route::get('/my-transactions',  [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transaction',     [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/payment/{id}',      [StripeController::class, 'showPayment'])->name('payment');
    Route::get('/stripe/return',     [StripeController::class, 'return'])->name('stripe.return');
    Route::get('/my-contact-list',  [ContactController::class, 'index'])->name('contacts');
    Route::get('/new-contact',      [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/new-contact',     [ContactController::class, 'store'])->name('contacts.store');
    Route::post('/contacts/quick',   [ContactController::class, 'quickStore'])->name('contacts.quick-store');
    Route::get('/new-contact/{id}', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/new-contact/{id}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contact/{id}',  [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/reclamation',      [ReclamationController::class, 'show'])->name('reclamation');
    Route::post('/reclamation',     [ReclamationController::class, 'send'])->name('reclamation.send');
});

// ─── Pages admin ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',          [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers',          [AdminUserController::class, 'index'])->name('users');
    Route::patch('/customers/{id}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/customers/{id}',  [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/transactions',       [TransactionController::class, 'adminIndex'])->name('transactions');
    Route::get('/settings',           [AdminSettingsController::class, 'index'])->name('settings');
    Route::post('/settings/promotion',[AdminSettingsController::class, 'addPromotion'])->name('settings.promotion.add');
    Route::patch('/settings/promotion/{id}/toggle', [AdminSettingsController::class, 'togglePromotion'])->name('settings.promotion.toggle');
    Route::delete('/settings/promotion/{id}',       [AdminSettingsController::class, 'deletePromotion'])->name('settings.promotion.delete');
    Route::delete('/settings/promotions/{type}/all',[AdminSettingsController::class, 'deleteAllPromotions'])->name('settings.promotions.delete-all');
    Route::post('/settings/banner',                 [AdminSettingsController::class, 'saveBanner'])->name('settings.banner.save');
    // Profil admin
    Route::put('/settings/profile',          [AdminSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    // Taux de change
    Route::post('/settings/rates',           [AdminSettingsController::class, 'updateRates'])->name('settings.rates.update');
    // Opérateurs Mobile Money
    Route::post('/settings/operators',       [AdminSettingsController::class, 'addOperator'])->name('settings.operator.add');
    Route::patch('/settings/operators/{id}/toggle', [AdminSettingsController::class, 'toggleOperator'])->name('settings.operator.toggle');
    Route::patch('/settings/operators/{id}/coming-soon', [AdminSettingsController::class, 'toggleComingSoon'])->name('settings.operator.coming-soon');
    Route::delete('/settings/operators/{id}',[AdminSettingsController::class, 'deleteOperator'])->name('settings.operator.delete');
    // Frais de transfert
    Route::post('/settings/fees',            [AdminSettingsController::class, 'updateFees'])->name('settings.fees.update');
    // Mode maintenance
    Route::patch('/settings/maintenance',    [AdminSettingsController::class, 'toggleMaintenance'])->name('settings.maintenance.toggle');
    // Paramètres avancés
    Route::get('/advanced',                  [AdminSettingsController::class, 'advancedSettings'])->name('advanced');
});

