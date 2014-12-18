<?php namespace Owlgrin\Throttle;

use Illuminate\Support\ServiceProvider;

class ThrottleServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('owlgrin/throttle');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommands();
		$this->registerRepositories();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

	protected function registerCommands()
	{
		$this->app->bindShared('command.throttle.table', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\ThrottleTableCommand');
		});

		$this->app->bindShared('command.user.subscribe', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\UserSubscribeCommand');
		});

		$this->app->bindShared('command.plan.entry', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\PlanEntryCommand');
		});

		$this->app->bindShared('command.pack.entry', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\PackEntryCommand');
		});

		$this->app->bindShared('command.add.user.pack', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\AddPackForUserCommand');
		});

		$this->app->bindShared('command.remove.user.pack', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\RemovePackForUserCommand');
		});

		$this->app->bindShared('command.user.bill', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\UserBillCommand');
		});

		$this->app->bindShared('command.pack.list', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\PackListCommand');
		});

		$this->app->bindShared('command.plan.list', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\PlanListCommand');
		});

		$this->app->bindShared('command.feature.list', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\FeatureListCommand');
		});

		$this->app->bindShared('command.seed.daily.usage', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\SeedDailyUsageCommand');
		});

		$this->app->bindShared('command.add.user.period', function($app)
		{
			return $app->make('Owlgrin\Throttle\Commands\AddPeriodForUserCommand');
		});

		$this->commands('command.throttle.table');
		$this->commands('command.user.subscribe');
		$this->commands('command.plan.entry');
		$this->commands('command.pack.entry');
		$this->commands('command.add.user.pack');
		$this->commands('command.remove.user.pack');
		$this->commands('command.user.bill');
		$this->commands('command.pack.list');
		$this->commands('command.plan.list');
		$this->commands('command.feature.list');
		$this->commands('command.seed.daily.usage');
		$this->commands('command.add.user.period');
	}

	protected function registerRepositories()
	{		
		$this->app->bind('Owlgrin\Throttle\Biller\Biller', 'Owlgrin\Throttle\Biller\PayAsYouGoBiller');
		$this->app->bind('Owlgrin\Throttle\Subscriber\SubscriberRepo', 'Owlgrin\Throttle\Subscriber\DbSubscriberRepo');
		$this->app->bind('Owlgrin\Throttle\Plan\PlanRepo', 'Owlgrin\Throttle\Plan\DbPlanRepo');
		$this->app->bind('Owlgrin\Throttle\Pack\PackRepo', 'Owlgrin\Throttle\Pack\DbPackRepo');
		$this->app->bind('Owlgrin\Throttle\Period\PeriodRepo', 'Owlgrin\Throttle\Period\DbPeriodRepo');
		$this->app->bind('Owlgrin\Throttle\Feature\FeatureRepo', 'Owlgrin\Throttle\Feature\DbFeatureRepo');
		$this->app->bind('Owlgrin\Throttle\Limiter\LimiterInterface', 'Owlgrin\Throttle\Limiter\Limiter');

		$this->app->singleton('throttle', 'Owlgrin\Throttle\Throttle');
	}
}
