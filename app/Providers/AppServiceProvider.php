<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Notifications;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('partials.navbar', function ($view) {
			$notifications = new Notifications;
			$data = $notifications->get(Auth::user()->settings("notification_menu_max"));
			$count = $data->count();
			$view->with(["notifications" => $data,
						 "notification_count" => $count]);
		});
		view()->composer('partials.sidebar', function($view) {
			$user = Auth::user();
			$companies = $user->companies();
			$companyId = $user->settings("current_company");
			
			if ($companies->count() > 0 && $companyId == ""){
				$currentCompany = $companies->first();										
			}
			else {
				$currentCompany = $companies->where("companies.COMPANY_ID", $companyId)->first();				
			}
			$view->with(["companies_list" => $user->companies()->get(),
						 "current_company" => $currentCompany]);
		});
    }
}
