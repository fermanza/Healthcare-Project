<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter as BasePresenter;

abstract class Presenter extends BasePresenter
{

	protected $percentages = [
        'Percent Recruited - Total',
        'Percent Recruited - Phys',
        'Percent Recruited - APP',
        'Prev - FT Util - %',
        'Prev - Embassador Util - %',
        'Prev - Int Locum Util - %',
        'Prev - Ext Locum Util - %',
	];

	protected $currency = [
        'Prev Month - Inc Comp',
        'YTD - Inc Comp',
	];

	public function excel($property)
	{
		$actualValue = parent::__get($property);

		if (is_numeric($actualValue) && $actualValue == 0) {
			return null;
		}

		return $actualValue;
	}

	/**
	 * Allow for property-style retrieval
	 *
	 * @param $property
	 * @return mixed
	 */
	public function __get($property)
	{
		// if (method_exists($this, $property))
		// {
		// 	return $this->{$property}();
		// }

		// $actualValue = $this->entity->{$property};



		$actualValue = parent::__get($property);

		if (is_numeric($actualValue) && $actualValue == 0) {
			return null;
		} elseif (is_numeric($actualValue) && $actualValue > 0 && in_array($property, $this->percentages)) {
			return number_format($actualValue * 100, 1).'%';
		} elseif (is_numeric($actualValue) && $actualValue > 0 && in_array($property, $this->currency)) {
			return '$'.number_format($actualValue, 2);
		} elseif (is_numeric($actualValue) && $actualValue > 0) {
			return number_format($actualValue, 1);
		}

		return $actualValue;
	}

}