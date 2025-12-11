<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Patrimoine;
use App\Models\DemandeFourniture;
use App\Models\Conge;
use App\Models\OrganigrammeMembers;
use App\Policies\PatrimoinePolicy;
use App\Policies\DemandeFourniturePolicy;
use App\Policies\CongePolicy;
use App\Policies\MemberPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Patrimoine::class => PatrimoinePolicy::class,
        DemandeFourniture::class => DemandeFourniturePolicy::class,
        Conge::class => CongePolicy::class,
        OrganigrammeMembers::class => MemberPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer les policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
