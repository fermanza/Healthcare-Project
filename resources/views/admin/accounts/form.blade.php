<form action="">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="site_code">@lang('Site Code')</label>
                <input type="text" class="form-control" id="site_code">
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
                <label for="address">@lang('Address')</label>
                <input type="text" class="form-control" id="address" placeholder="Google API">
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
                <label for="recruiter">@lang('Recruiter')</label>
                <select id="recruiter" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="manager">@lang('Manager')</label>
                <select id="manager" class="form-control select2">
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
                <label for="practice">@lang('Practice')</label>
                <select id="practice" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach ($practices as $practice)
                        <option value="{{ $practice->id }}">{{ $practice->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="division">@lang('Division')</label>
                <select id="division" class="form-control select2">
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
                <input type="text" class="form-control" id="city" placeholder="Autopopulate City">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="state">@lang('State')</label>
                <input type="text" class="form-control" id="state" placeholder="Autopopulate State">
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="start_date">@lang('Start Date')</label>
                <input type="datetime-local" class="form-control" id="start_date">
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="bg-success">
                <br />
                Account created
                <strong>3</strong>
                months ago.
                <br />&nbsp;
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="physicians">@lang('No. of Physicians needed')</label>
                <input type="number" class="form-control" id="physicians">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="apps">@lang('No. of APPs needed')</label>
                <input type="number" class="form-control" id="apps">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="physicians_hours">@lang('No. of hours for Physicians per month')</label>
                <input type="number" class="form-control" id="physicians_hours">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="app_hours">@lang('No. of hours for APP per month')</label>
                <input type="number" class="form-control" id="app_hours">
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Has a press release gone out announcing newstart, and if so when?')
                    </label>
                    <input type="text" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Have mailers gone out announcing management change?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Have mailers gone out for recruiting?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Have email blasts gone out?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('PURL Campaign')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Account Marketing slick generated')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Do we need to set up a collaboration recruiting team, and if so, who is on the team?')
                    </label>
                    <input type="text" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('What is the compensation grid, including sign on bonuses or retention bonuses?')
                    </label>
                    <input type="text" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('What additional recruiting incentives do we have in place?')
                    </label>
                    <input type="text" />
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Have you notified the locum companies?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
                        @lang('Have you notified the 3rd party search firms?')
                    </label>
                </div>

                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox">
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