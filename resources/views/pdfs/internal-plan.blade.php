<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style>
    body {
        font-family: Helvetica, Arial, Sans-Serif;
    }

    .table {
        width: 100%;
    }

    .table-bordered {
        border-collapse: collapse;
    }

    .table-bordered tr td {
        border: 1px solid #333;
        padding: 5px;
    }

    .text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .halft-width {
        width: 50%;
    }

    .inline-b {
        display: inline-block;
    }

    .circle {
        width: 15px;
        height: 15px;
        border: 1px solid #111;
        border-radius: 50%;
        margin: 0 auto;
    }

    .circle-blue {
        background-color: #2489C5;
    }
</style>

<div class="text-center">
    <img src="{{ asset('img/emcare-logo.png') }}" class="halft-width" />
</div>

<div style="height: 50px;"></div>

<table class="table text-center">
    <tr>
        <td>
            <strong>{{ $account->name }}</strong>
        </td>
        <td>
            <strong>@lang('Site Code'):</strong>
            {{ $account->site_code }}
        </td>
    </tr>
</table>

<div style="height: 50px;"></div>

<h3 class="text-center">@lang('Internal Plan')</h3>

<table class="table table-bordered">
    <tr>
        <td width="25px">
            <div class="circle {{ $account->press_release ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Has a press release gone out announcing newstart, and if so when?')
        </td>
        <td width="220px">
            {{ $account->press_release_date ? $account->press_release_date->format('Y-m-d') : '' }}
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->management_change_mailers ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have mailers gone out announcing management change?')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->recruiting_mailers ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have mailers gone out for recruiting?')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->email_blast ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have email blasts gone out?')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->purl_campaign ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('PURL Campaign')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->marketing_slick ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Account Marketing slick generated')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->collaboration_recruiting_team ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Do we need to set up a collaboration recruiting team, and if so, who is on the team?')
        </td>
        <td>
            {{ $account->collaboration_recruiting_team_names }}
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->compensation_grid ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('What is the compensation grid, including sign on bonuses or retention bonuses?')
        </td>
        <td>
            {{ $account->compensation_grid_bonuses }}
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->recruiting_incentives ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('What additional recruiting incentives do we have in place?')
        </td>
        <td>
            {{ $account->recruiting_incentives_description }}
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->locum_companies_notified ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have you notified the locum companies?')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->search_firms_notified ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have you notified the 3rd party search firms?')
        </td>
        <td>
            
        </td>
    </tr>
    <tr>
        <td>
            <div class="circle {{ $account->departments_coordinated ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            @lang('Have you coordinated with the on site hospital marketing department physicians liaisons and internal recruiter?')
        </td>
        <td>
            
        </td>
    </tr>
</table>