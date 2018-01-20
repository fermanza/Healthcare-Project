@extends('layouts.admin')

@section('content-header', __('Dashboards'))

@section('content')
<div class="charts">
	<div class="fb-parent">
		<div class="filters fb-parent fb-rows">
            <form id="chartsForm">
    			<div class="box fb-parent fb-rows">
    				<div class="inv-header mbm">
    					<i class="fa fa-filter"></i> Filters
    				</div>
    				<div class="switch-control mbm">
    					<ul class="switch-options fb-parent">
    						<li class="fb-grow {{Request::input('period') ? (Request::input('period') == 'MTD' ? 'selected' : '') : ''}}">MTD</li>
    						<li class="fb-grow {{Request::input('period') ? (Request::input('period') == 'QTD' ? 'selected' : '') : 'selected'}}">QTD</li>
    						<li class="fb-grow {{Request::input('period') ? (Request::input('period') == 'YTD' ? 'selected' : '') : ''}}">YTD</li>
    					</ul>
    					<p class="title">Custom Period</p>
                        <input type="text" id="periodValue" class="hidden" name="period" value="QTD">
    				</div>
    				<div class="filter-list fb-grow mbm">
                        <select class="form-control select2" name="affiliations[]" data-placeholder="@lang('Affiliation')" multiple>
                            @foreach ($affiliations as $affiliation)
                                <option value="{{ $affiliation->name }}" {{ in_array($affiliation->name, Request::input('affiliations') ?: []) ? 'selected' : '' }}>{{ $affiliation->name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="recruiters[]" data-placeholder="@lang('Recruiter')" multiple>
                            @foreach ($recruiters as $recruiter)
                                <option value="{{ $recruiter->fullName() }}" {{ in_array($recruiter->fullName(), Request::input('recruiters') ?: []) ? 'selected' : '' }}>{{ $recruiter->fullName() }}</option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="practices[]" data-placeholder="@lang('Service Line')" multiple>
                            @foreach ($practices as $practice)
                                <option value="{{ $practice->name }}" {{ in_array($practice->name, Request::input('practices') ?: []) ? 'selected' : '' }}>{{ $practice->name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="managers[]" data-placeholder="@lang('Manager')" multiple>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->fullName() }}" {{ in_array($manager->fullName(), Request::input('managers') ?: []) ? 'selected' : '' }}>{{ $manager->fullName() }}</option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="regions[]" data-placeholder="@lang('Operating Unit')" multiple>
                            @foreach ($regions as $region)
                                <option value="{{ $region->name }}" {{ in_array($region->name, Request::input('regions') ?: []) ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="RSCs[]" data-placeholder="@lang('RSC')" multiple>
                            @foreach ($RSCs as $RSC)
                                <option value="{{ $RSC->id }}" {{ in_array($RSC->id, Request::input('RSCs') ?: []) ? 'selected' : '' }}>{{ $RSC->name }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="DOO[]" data-placeholder="@lang('DOO')" multiple>
                            @foreach ($doos as $doo)
                                <option value="{{ $doo->fullName() }}" {{ in_array($doo->fullName(), Request::input('DOO') ?: []) ? 'selected' : '' }}>{{ $doo->fullName() }}</option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="SVP[]" data-placeholder="@lang('SVP')" multiple>
                            @foreach ($SVPs as $SVP)
                                <option value="{{ $SVP->SVP }}" {{ in_array($SVP->SVP, Request::input('SVP') ?: []) ? 'selected' : '' }}>{{ $SVP->SVP }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="RMD[]" data-placeholder="@lang('RMD')" multiple>
                            @foreach ($RMDs as $RMD)
                                <option value="{{ $RMD->RMD }}" {{ in_array($RMD->RMD, Request::input('RMD') ?: []) ? 'selected' : '' }}>{{ $RMD->RMD }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="cities[]" data-placeholder="@lang('City')" multiple>
                            @foreach ($cities as $city)
                                <option value="{{ $city->city }}" {{ in_array($city->city, Request::input('cities') ?: []) ? 'selected' : '' }}>
                                    {{ $city->city }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-control select2" name="states[]" data-placeholder="@lang('State')" multiple>
                            @foreach ($states as $state)
                                <option value="{{ $state->abbreviation }}" {{ in_array($state->abbreviation, Request::input('states') ?: []) ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="groups[]" data-placeholder="@lang('Group')" multiple>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" {{ in_array($group->id, Request::input('groups') ?: []) ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="sites[]" data-placeholder="@lang('Site Code')" multiple>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}" {{ in_array($site->id, Request::input('sites') ?: []) ? 'selected' : '' }}>
                                    {{ $site->siteCode }} - {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    
                        <select class="form-control select2" name="new" data-placeholder="@lang('Same\New')">
                            <option value=""></option>
                            <option value="1" {{ Request::input('new') == 1 ? 'selected' : '' }}>New Store</option>
                            <option value="2" {{ Request::input('new') == 2 ? 'selected' : '' }}>Same Store</option>
                        </select>
    				</div>
    				<div class="controls">
                        <a href="{{ route('admin.dashboards.index') }}" type="submit" class="btn btn-default btn-block clear mbs">
                            Clear Filters
                        </a>
    					<button type="submit" class="btn btn-primary btn-block apply">Apply Filters</button>
    				</div>
    			</div>
            </form>
		</div>
		<div class="main fb-grow fb-rows fb-parent mrm">
			<div class="fb-grow row-1 fb-parent mbm">
				<div class="fb-grow fb-parent fb-rows col-1 box mrm fb-h-center" id="pipeline">
					<h4 class="title">Pipeline</h4>
					<div class="fb-grow fb-h-center fb-parent">
						<div class="pipeline-wrapper fb-v-center"></div>
					</div>
				</div>
				<div class="fb-grow col-2 fb-rows fb-parent wrap">
					<div class="fb-grow subrow-1 box mbm text-center fb-h-center fb-parent">
						<div class="fb-v-center">
							<h1 class="mtn dynamic noPlus" data-id="PhysiciansRecruited"></h1>
							<p>Physicians Recruited</p>
							<h3>QTD</h3>
						</div>
					</div>
					<div class="fb-grow subrow-2 box mtm text-center fb-h-center fb-parent">
						<div class="fb-v-center">
							<h1 class="mtn dynamic noPlus" data-id="AppRecruited"></h1>
							<p>App Recruited</p>
							<h3>QTD</h3>
						</div>
					</div>
				</div>
				<div class="fb-grow col-3 box mlm fb-rows fb-parent">
					<h3 class="title">Total Percent Recruited</h3>
					<div class="fb-grow fb-parent">
						<div class="fb-parent fb-grow fb-rows text-center fb-h-center">
							<div class="fb-v-center">
								<h1 class="totalPctRecruited"></h1>
								<h1>QTD</h1>
							</div>
						</div>
						<div class="fb-grow fb-h-center fb-parent">
							<div class="tpr_graph fb-v-center"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="fb-grow row-2 fb-parent mbm">
				<div class="fb-grow fb-rows fb-parent col-1 box mrm">
					<h4 class="title">Recent Recruited V's Contracts In</h4>
					<div class="fb-grow rrvci">
						
					</div>
				</div>
				<div class="fb-grow fb-rows fb-parent col-2 box mlm">
					<h4 class="title">Recent Recruited V's Openings</h4>
					<div class="fb-grow rrvo">
						
					</div>
				</div>
			</div>
			<div class="row-3 fb-parent mbm">
				<div class="fb-grow col-1 box text-center fb-h-center fb-parent">
					<div class="fb-v-center">
						<h2 class="mtn dynamic" data-id="QTDApplications"></h2>
						<p class="mbn">QTD Applications</p>
					</div>
				</div>
				<div class="fb-grow col-2 box text-center fb-h-center fb-parent">
					<div class="fb-v-center">
						<h2 class="mtn dynamic" data-id="QTDInterviews"></h2>
						<p class="mbn">QTD Interviews</p>
					</div>
				</div>
				<div class="fb-grow col-3 box text-center fb-h-center fb-parent">
					<div class="fb-v-center">
						<h2 class="mtn dynamic" data-id="QTDContractsOut"></h2>
						<p class="mbn">Contacts Out</p>
					</div>
				</div>
				<div class="fb-grow col-4 box text-center fb-h-center fb-parent">
					<div class="fb-v-center">
						<h2 class="mtn dynamic" data-id="QTDContractsIn"></h2>
						<p class="mbn">Contacts In</p>
					</div>
				</div>
				<div class="fb-grow col-5 box text-center fb-h-center fb-parent">
					<div class="fb-v-center">
						<h2 class="mtn dynamic" data-id="QTDCredentialing"></h2>
						<p class="mbn">QTD Credentialing</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.switch-options>.fb-grow').on('click', function() {
            $('#periodValue').val($(this).text());
        })
    });
</script>
@endpush