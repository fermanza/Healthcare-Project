<?php

namespace App\Filters;

use Carbon\Carbon;

class SummaryFilter extends Filter
{
    /**
     * Apply practices filter.
     *
     * @param  array  $names
     * @return void
     */
    public function practices($names)
    {
        $this->query->whereIn('vAccountSummary.practice', $names);
    }

    /**
     * Apply divisions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function divisions($ids)
    {
        $this->query->whereIn('tAccount.divisionId', $ids);
    }

    /**
     * Apply recruiters filter.
     *
     * @param  array  $names
     * @return void
     */
    public function recruiters($names)
    {
        $this->query->whereIn('vAccountSummary.RSC Recruiter', $names);
    }

    /**
     * Apply managers filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function managers($ids)
    {
        $this->query->whereIn('tManager.employeeId', $ids);
    }

    /**
     * Apply regions filter.
     *
     * @param  array  $names
     * @return void
     */
    public function regions($names)
    {
        $this->query->whereIn('vAccountSummary.Operating Unit', $names);
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
     * Apply contractOutDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function startDate($date)
    {
        $dt = Carbon::parse($date);

        $firstDayOfMonth = new Carbon('first day of ' . $dt->format('F') . ' ' . $dt->format('Y'));
        $lastDayOfMonth = new Carbon('last day of ' . $dt->format('F') . ' ' . $dt->format('Y'));

        $this->query->whereBetween('vAccountSummary.Start Date', [$firstDayOfMonth, $lastDayOfMonth]);
    }

    // /**
    //  * Apply sort filter.
    //  *
    //  * @param  string  $key
    //  * @return void
    //  */
    // public function sort($key)
    // {
    //     $sorts = [
    //         'value' => 'tContractLogs.value',
    //         'status' => 'tContractStatus.contractStatus',
    //         'provider_first_name' => 'tContractLogs.providerFirstName',
    //         'provider_last_name' => 'tContractLogs.providerLastName',
    //         'position' => 'tPosition.position',
    //         'hours' => 'tContractLogs.numOfHours',
    //         'practice' => 'tPractice.name',
    //         'hospital_name' => 'tAccount.name',
    //         'site_code' => 'tAccount.siteCode',
    //         'group' => 'tGroup.name',
    //         'division' => 'tDivision.name',
    //         'contract_out' => 'tContractLogs.contractOutDate',
    //         'contract_in' => 'tContractLogs.contractInDate',
    //         'projected_start_date' => 'tContractLogs.projectedStartDate',
    //         'reason' => 'tContractNote.contractNote',
    //     ];

    //     $order = $this->request->input('order', 'asc');
    //     $column = array_get($sorts, $key, 'tContractLogs.id');

    //     $this->query->orderBy($column, $order);
    // }
}
