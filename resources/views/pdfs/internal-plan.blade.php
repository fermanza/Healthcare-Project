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
        display: inline-block;
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
            <strong>Site Code:</strong>
            {{ $account->site_code }}
        </td>
    </tr>
</table>

<div style="height: 50px;"></div>

<h3 class="text-center">Internal Plan</h3>

<table class="table table-bordered">
    <tr>
        <td class="text-center" width="25px">
            <div class="circle {{ $account->press_release ? 'circle-blue': '' }}"></div>
        </td>
        <td>
            Has a press release gone out announcing newstart, and if so when?
        </td>
        <td width="200px">
            {{ $account->press_release_date->format('Y-m-d') }}
        </td>
    </tr>
</table>
