<?php namespace Owlgrin\Throttle\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Owlgrin\Throttle\Biller\Biller;

/**
 * Command to generate the required migration
 */
class UserBillCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'throttle:bill';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Find\'s Bill of the user';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	protected $biller;

	public function __construct(Biller $biller)
	{
 		parent::__construct();
		$this->biller  = $biller;
	}

	public function fire()
	{
		$userId = $this->option('user');
		$startDate = $this->option('start_date');
		$endDate = $this->option('end_date');

		$bill = $this->biller->bill($userId, $startDate, $endDate);

		$this->info('User With id '.$userId.' has a bill of');
		print_r($bill);
	}

	protected function getOptions()
	{
		return array(
			array('user', null, InputOption::VALUE_OPTIONAL, 'The id of the user who wants to subscribe', null),
			array('start_date', null, InputOption::VALUE_OPTIONAL, 'The start date of the bill.', null),
			array('end_date', null, InputOption::VALUE_OPTIONAL, 'The end date of the bill.', null)
		);
	}
}