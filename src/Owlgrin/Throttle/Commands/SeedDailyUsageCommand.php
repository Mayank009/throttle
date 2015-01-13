<?php namespace Owlgrin\Throttle\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Owlgrin\Throttle\Subscriber\SubscriberRepo;
/**
 * Command to generate the required migration
 */
class SeedDailyUsageCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'throttle:seed:daily:usage';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed Daily Usage';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	protected $subscriber;

	public function __construct(SubscriberRepo $subscriber)
	{
 		parent::__construct();

 		$this->subscriber = $subscriber;
	}

	public function fire()
	{
		$userId = $this->option('user_id');

		$subscription = $this->subscriber->subscription($userId);

		$this->subscriber->addInitialUsageForFeatures($subscription['id'], $subscription['plan_id']);

		$this->info('User with subscription id '. $subscription['id'] .' has been has been seed with planID '. $subscription['plan_id']);
	}
	
	protected function getOptions()
	{
		return array(
			array('user_id', null, InputOption::VALUE_OPTIONAL, 'The user\'s id.', null),
		);
	}
}