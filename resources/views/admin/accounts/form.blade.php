<form action="{{ route('admin.accounts.store') }}" method="POST">
    {{ csrf_field() }}
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="site_code">@lang('Site Code')</label>
                <input type="text" class="form-control" id="site_code" name="site_code" />
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('img/upload-placeholder.jpg') }}" alt="Upload Photo" class="img-thumbnail" width="300">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="google_address">@lang('Address')</label>
                <input type="text" class="form-control" id="google_address" placeholder="Google API">
            </div>
            <div>
                <img src="{{ asset('img/map-placeholder.png') }}" alt="Upload Photo" class="img-thumbnail" width="400">
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="recruiter_id">@lang('Recruiter')</label>
                <select id="recruiter_id" name="recruiter_id" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="manager_id">@lang('Manager')</label>
                <select id="manager_id" name="manager_id" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="practice_id">@lang('Practice')</label>
                <select id="practice_id" name="practice_id" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($practices as $practice)
                        <option value="{{ $practice->id }}">{{ $practice->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="division_id">@lang('Division')</label>
                <select id="division_id" name="division_id" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="city">@lang('City')</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Autopopulate City" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="state">@lang('State')</label>
                <input type="text" class="form-control" id="state" name="state" placeholder="Autopopulate State" />
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="start_date">@lang('Start Date')</label>
                <div class="input-group">
                    <input type="text" class="form-control datetimepicker" id="start_date" name="start_date" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            {{-- <div class="bg-success">
                <br />
                Account created
                <strong>3</strong>
                months ago.
                <br />&nbsp;
            </div> --}}
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="physicians_needed">@lang('No. of Physicians needed')</label>
                <input type="number" class="form-control" id="physicians_needed" name="physicians_needed" min="0" value="0" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="apps_needed">@lang('No. of APPs needed')</label>
                <input type="number" class="form-control" id="apps_needed" name="apps_needed" min="0" value="0" />
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="physician_hours_per_month">@lang('No. of hours for Physicians per month')</label>
                <input type="number" class="form-control" id="physician_hours_per_month" name="physician_hours_per_month" min="0" value="0" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="app_hours_per_month">@lang('No. of hours for APP per month')</label>
                <input type="number" class="form-control" id="app_hours_per_month" name="app_hours_per_month" min="0" value="0" />
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="press_release" />
                        @lang('Has a press release gone out announcing newstart, and if so when?')
                    </label>
                    <input type="text" name="press_release_date" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="management_change_mailers" />
                        @lang('Have mailers gone out announcing management change?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruiting_mailers" />
                        @lang('Have mailers gone out for recruiting?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="email_blast" />
                        @lang('Have email blasts gone out?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="purl_campaign" />
                        @lang('PURL Campaign')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="marketing_slick" />
                        @lang('Account Marketing slick generated')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="collaboration_recruiting_team" />
                        @lang('Do we need to set up a collaboration recruiting team, and if so, who is on the team?')
                    </label>
                    <input type="text" name="collaboration_recruiting_team_names" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="compensation_grid" />
                        @lang('What is the compensation grid, including sign on bonuses or retention bonuses?')
                    </label>
                    <input type="text" name="compensation_grid_bonuses" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruiting_incentives" />
                        @lang('What additional recruiting incentives do we have in place?')
                    </label>
                    <input type="text" name="recruiting_incentives_description" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="locum_companies_notified" />
                        @lang('Have you notified the locum companies?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="search_firms_notified" />
                        @lang('Have you notified the 3rd party search firms?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="departments_coordinated" />
                        @lang('Have you coordinated with the on site hospital marketing department physicians liaisons and internal recruiter?')
                    </label>
                </div>

            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="javascript:;" class="btn btn-primary">@lang('Create PDF')</a>
            <br />
            @lang('To give internal plan')
        </div>
    </div>

    <hr />
    
    <div class="row text-center">
        <div class="col-md-6">
            <a href="javascript:;" class="btn btn-primary">@lang('Print as PDF')</a>
            <br />
            @lang('Marketing Slick')
        </div>
        <div class="col-md-6">
            <a href="javascript:;" class="btn btn-primary">@lang('Email Marketing Slick')</a>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-success">@lang('Submit')</button>
        </div>
    </div>

</form>