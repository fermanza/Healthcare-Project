<?php

namespace App\Filters;

class ContractLogsFilter extends Filter
{
    /**
     * Apply practices filter.
     *
     * @param  array  $names
     * @return void
     */
    public function practices($names)
    {
        $this->query->where(function ($query) use ($names) {
            foreach ($names as $name) {
                $query->orWhere('tPractice.name', 'like', "{$name}%");
            }
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
        $this->query->whereIn('tGroup.regionId', $ids);
    }
    
    /**
     * Apply RSCs filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function RSCs($ids)
    {
        $this->query->whereIn('tAccount.RSCId', $ids);
    }
    
    /**
     * Apply provider filter.
     *
     * @param  string  $text
     * @return void
     */
    public function provider($text)
    {
        $this->query->where(function ($query) use ($text) {
            $query->where('tContractLogs.providerFirstName', 'like', "%{$text}%")
                ->orWhere('tContractLogs.providerLastName', 'like', "%{$text}%");
        });
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

        $this->query->whereBetween('tContractLogs.contractInDate', array($startDate, $endDate));
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
