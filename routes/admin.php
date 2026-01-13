<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Events Management (Simplified - view + unpublish only)
        Route::get('events', function () {
            $events = \App\Models\Event::with(['organizer', 'category'])
                ->latest()
                ->paginate(20);
            return view('admin.events.index', compact('events'));
        })->name('events.index');

        Route::post('events/{event}/unpublish', function (\App\Models\Event $event) {
            if ($event->status->value !== 'published') {
                return back()->with('error', 'Event tidak dalam status published');
            }

            $event->update([
                'status' => \App\Enums\EventStatus::DRAFT,
                'published_at' => null,
            ]);

            return back()->with('success', 'Event berhasil di-unpublish oleh admin');
        })->name('events.unpublish');

        Route::delete('events/{event}', function (\App\Models\Event $event) {
            if ($event->orders()->whereIn('status', ['paid', 'completed'])->exists()) {
                return back()->with('error', 'Event tidak dapat dihapus karena ada tiket yang sudah terjual');
            }

            $event->delete();
            return back()->with('success', 'Event berhasil dihapus');
        })->name('events.destroy');

        // Placeholder routes untuk fase 2
        Route::view('categories', 'admin.placeholder', ['title' => 'Categories'])->name('categories.index');
        Route::view('users', 'admin.placeholder', ['title' => 'Users'])->name('users.index');
        Route::view('organizers', 'admin.placeholder', ['title' => 'Organizers'])->name('organizers.index');
        Route::view('vouchers', 'admin.placeholder', ['title' => 'Vouchers'])->name('vouchers.index');
        Route::view('settlements', 'admin.placeholder', ['title' => 'Settlements'])->name('settlements.index');
    });
