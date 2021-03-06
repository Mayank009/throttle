<?php namespace Owlgrin\Throttle\Biller;

use Owlgrin\Throttle\Biller\Biller;
use Owlgrin\Throttle\Subscriber\SubscriberRepo;
use Owlgrin\Throttle\Feature\FeatureRepo;

class PayAsYouGoBiller implements Biller{

	protected $subscription;
	protected $featureRepo;

	public function __construct(SubscriberRepo $subscription, FeatureRepo $featureRepo)
	{
		$this->subscription = $subscription;
		$this->featureRepo = $featureRepo;
	}

	/**
	 * Calculate the bill between the given dates
	 *
	 * @param  integer $subscriptionId
	 * @param  string $startDate
	 * @param  string $endDate
	 * @return array
	 */
	public function bill($subscriptionId, $startDate, $endDate)
	{
		$usages = $this->subscription->getUsage($subscriptionId, $startDate, $endDate);

		return $this->calculateByUsages($usages);
	}

	/**
	 * Gets the estimate based on the usages passed.
	 *
	 * @param  array $usages
	 * @return array
	 */
	public function estimate($usages)
	{		
		return $this->calculateByUsages($usages);
	}

	/**
	 * Calculate the bill using usages
	 *
	 * @param  array $usages
	 * @return array
	 */
	private function calculateByUsages($usages)
	{
		$amount = 0;
		$lines = [];

		foreach($usages as $index => $feature)
		{
			$tiers = $this->getTiersByFeature($feature['plan_id'], $feature['feature_id']);
			
			$lineItem = $this->calculateByTiers($tiers, $feature['used_quantity']);
		
			$amount += $lineItem['amount'];
			$lineItem['usage'] = (int) $feature['used_quantity'];
			$lines[] = $lineItem;
		}

		return ['lines' => $lines, 'amount' => $amount];
	}

	/**
	 * Returns the various tiers of a feature
	 *
	 * @param  integer $planId
	 * @param  integer $featureId
	 * @return array
	 */
	private function getTiersByFeature($planId, $featureId)
	{
		//finding limit of the feature
		return $this->featureRepo->featureLimit($planId, $featureId);
	}

	/**
	 * Calculates the amount of a feature for all tiers
	 *
	 * @param  array $tiers
	 * @param  integer $featureId
	 * @param  integer $usage
	 * @return array
	 */
	private function calculateByTiers($tiers, $usage)
	{
		list($lineItems, $amount) = $this->prepareLineItems($tiers, $usage);

		return ['tiers' => $lineItems, 'feature_name' => $tiers[0]['name'], 'amount' => $amount];
	}

	/**
	 * Prepares the line items for a feature using tiers
	 *
	 * @param  array $tiers
	 * @param  integer $usage
	 * @return array
	 */
	private function prepareLineItems($tiers, $usage)
	{
		$amount = 0;
		$lineItems = [];

		foreach($tiers as $index => $tier) 
		{
			list($lineItem, $usage, $wasLast) = $this->prepareLineItem($tier, $usage);
			
			$lineItems[] = $lineItem; // adding in the line items
			$amount += $lineItem['amount']; // adding the tier's amount in the feature's total amount
			
			// if this was the last tier to be prepared, we will break
			if($wasLast) break;
		}

		return [$lineItems, $amount];
	}

	/**
	 * Prepares a single line iten for a tier
	 *
	 * @param  array $tier
	 * @param  integer $usage
	 * @return array
	 */
	private function prepareLineItem($tier, $usage)
	{
		if($this->isLastTierToBePrepared($tier, $usage) or $this->isTierWithNoLimits($tier))
		{
			$usageLeftToBeProcessed = 0;
			$usageProcessed = (int) $usage;
			$isLastTier = true;
		}
		else
		{
			$usageLeftToBeProcessed = (int) ($usage - $tier['limit']);
			$usageProcessed = (int) $tier['limit'];
			$isLastTier = false;
		}

		$lineItem = [
			'limit' => (int) $tier['limit'],
			'rate' => (int) $tier['rate'],
			'rate_per_quantity' => (int) $tier['per_quantity'],
			'usage' => $usageProcessed,
			'amount' => (int) $tier['rate'] / (int) $tier['per_quantity'] * (int) $usageProcessed
		];

		return [$lineItem, $usageLeftToBeProcessed, $isLastTier];
	}

	/**
	 * Checks if this tier is the last tier for a feature
	 *
	 * @param  array  $tier
	 * @param  integer  $usage
	 * @return boolean
	 */
	private function isLastTierToBePrepared($tier, $usage)
	{
		// when usage is less then the limit of this tier,
		// there's no need to calculate the next one as
		// the usage left to be calculated will be ZERO
		return ((int) $usage < (int) $tier['limit']);
	}

	/**
	 * Checks if this tier is without limits
	 *
	 * @param  array  $tier
	 * @return boolean
	 */
	private function isTierWithNoLimits($tier)
	{
		return is_null($tier['limit']);
	}
}