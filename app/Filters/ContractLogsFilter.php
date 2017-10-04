<?php

namespace App\Filters;

use Carbon\Carbon;

class ContractLogsFilter extends Filter
{
    /**
     * Apply practices filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function practices($ids)
    {
        $this->query->whereHas('account.practices', function($query) use ($ids) {
            $query->whereIn('practiceId', $ids);
        });
    }

    /**
     * Apply divisions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function divisions($ids)
    {
        $this->query->whereIn('tContractLogs.divisionId', $ids);
    }

    /**
     * Apply statuses filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function statuses($ids)
    {
        $this->query->whereIn('tContractLogs.statusId', $ids);
    }

    /**
     * Apply recruiters filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function recruiters($ids)
    {
        $this->query->whereIn('tContractLogs.recruiterId', $ids);
    }

    /**
     * Apply managers filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function managers($ids)
    {
        $this->query->whereIn('tContractLogs.managerId', $ids);
    }

    /**
     * Apply owners filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function owners($ids)
    {
        $this->query->whereIn('tContractLogs.logOwnerId', $ids);
    }

    /**
     * Apply positions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function positions($ids)
    {
        $this->query->whereIn('tContractLogs.positionId', $ids);
    }

    /**
     * Apply accounts filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function accounts($ids)
    {
        $this->query->whereIn('tContractLogs.accountId', $ids);
    }

    /**
     * Apply regions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function regions($ids)
    {
        $this->query->whereHas('account', function($query) use ($ids) {
            $query->whereIn('operatingUnitId', $ids);
        });
    }
    
    /**
     * Apply RSCs filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function RSCs($ids)
    {
        $this->query->whereHas('account', function($query) use ($ids) {
            $query->whereIn('RSCId', $ids);
        });
    }

    /**
     * Apply provider filter.
     *
     * @param  string  $text
     * @return void
     */
    public function provider($text)
    {
        $this->query->whereRaw("concat(tContractLogs.providerFirstName, ' ', tContractLogs.providerLastName) like ?", array('%'.$text.'%'));
    }

    /**
     * Apply contractOutDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function contractOutDate($date)
    {
        $dates = explode(" - ", $date);
        $startDate = $dates[0];
        $endDate = $dates[1];

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');

        $this->query->whereBetween('tContractLogs.contractOutDate', array($startDate, $endDate));
    }

    /**
     * Apply contractInDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function contractInDate($date)
    {
        $dates = explode(" - ", $date);
        $startDate = $dates[0];
        $endDate = $dates[1];

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');

        $this->query->whereBetween('tContractLogs.contractInDate', array($startDate, $endDate));
    }

    /**
     * Apply signedNotStarted filter.
     *
     * @param  string  $date
     * @return void
     */
    public function signedNotStarted($date)
    {
        $dates = explode(" - ", $date);
        $startDate = $dates[0];
        $endDate = $dates[1];

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');

        $this->query->whereBetween('tContractLogs.ProjectedStartDate', array($startDate, $endDate))->whereNotNull('tContractLogs.contractInDate');
    }

    /**
     * Apply pending filter.
     *
     * @param  string  $date
     * @return void
     */
    public function pending($value)
    {
        $this->query->whereNotNull('tContractLogs.contractOutDate')->whereNull('tContractLogs.contractInDate');
    }

    /**
     * Apply placements filter.
     *
     * @param  string  $value
     * @return void
     */
    public function placements($value)
    {
        $this->query->where('tContractLogs.value', '>', 0);
    }

    /**
     * Apply promos filter.
     *
     * @param  string  $value
     * @return void
     */
    public function promos($value)
    {
        $this->query->whereHas('status', function($query) {
            $query->where('contractStatus', 'like', 'Leadership Promos');
        });
    }

    /**
     * Apply sort filter.
     *
     * @param  string  $key
     * @return void
     */
    public function sort($key)
    {
        $sorts = [
            'value' => 'tContractLogs.value',
            'status' => 'tContractStatus.contractStatus',
            'provider_first_name' => 'tContractLogs.providerFirstName',
            'provider_last_name' => 'tContractLogs.providerLastName',
            'position' => 'tPosition.position',
            'hours' => 'tContractLogs.numOfHours',
            'practice' => 'tPractice.name',
            'hospital_name' => 'tAccount.name',
            'site_code' => 'tAccount.siteCode',
            'group' => 'tGroup.name',
            'division' => 'tDivision.name',
            'contract_out' => 'tContractLogs.contractOutDate',
            'contract_in' => 'tContractLogs.contractInDate',
            'projected_start_date' => 'tContractLogs.projectedStartDate',
            'reason' => 'tContractNote.contractNote',
        ];

        $order = $this->request->input('order', 'asc');
        $column = array_get($sorts, $key, 'tContractLogs.id');

        $this->query->orderBy($column, $order);
    }
}
