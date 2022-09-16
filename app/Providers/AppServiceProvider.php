<?php

namespace App\Providers;

use App\Domain\Customer\Customer;
use App\Domain\Product\Search\EloquentSearch;
use App\Domain\Product\Search\SearchContract;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Spatie\EventSourcing\Commands\CommandBus;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerWebBindings();

        $this->app->singleton(SearchContract::class, function () {
            return new EloquentSearch();
        });
    }

    private function registerWebBindings(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app->bind(Customer::class, function () {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (! $user) {
                throw new Exception("No logged in user but customer was requested");
            }

            if (! $user->customer) {
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                ]);
            }

            return $user->refresh()->customer;
        });

        $this->app->bind(User::class, function () {
            $user = Auth::user();

            if (! $user) {
                throw new Exception("No logged in user but it was requested");
            }

            return $user;
        });

        $this->app->singleton(CommandBus::class, function () {
            return new CommandBus();
        });
    }

    public function boot(): void
    {
    }
}
