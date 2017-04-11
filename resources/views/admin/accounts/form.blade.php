@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.accounts.store') : route('admin.accounts.update', [$account]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn {{ $action == 'create' ? 'btn-success' : 'btn-info' }}">
                {{ $action == 'create' ? __('Create') : __('Update') }}
            </button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $account->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('site_code') ? ' has-error' : '' }}">
                <label for="site_code">@lang('Site Code')</label>
                @if ($action == 'edit')
                    <small>
                        <a href="javascript:;" data-toggle="modal" data-target="#site-code-history">
                            @lang('History')
                        </a>
                    </small>
                @endif
                <input type="text" class="form-control" id="site_code" name="site_code" value="{{ old('site_code') ?: $account->site_code }}" required />
                @if ($errors->has('site_code'))
                    <span class="help-block"><strong>{{ $errors->first('site_code') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="image-upload center-block mt15" 
                data-upload-path="/admin/accounts/image" 
                data-current-path="{{ old('photo_path') ?: $account->photo_path ?: '/img/upload-placeholder.png' }}" 
                data-success="updatePathInput"
            >
            </div>
            <input type="hidden" id="photo_path" name="photo_path" value="{{ old('photo_path') ?: $account->photo_path }}" />
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('google_address') ? ' has-error' : '' }}">
                <label for="google_address">@lang('Address')</label>
                <input type="text" class="form-control" id="google_address" name="google_address" value="{{ old('google_address') ?: $account->google_address }}" />
                @if ($errors->has('google_address'))
                    <span class="help-block"><strong>{{ $errors->first('google_address') }}</strong></span>
                @endif
            </div>
            <div class="text-center">
                <div id="map"></div>
                {{-- <img src="{{ asset('img/map-placeholder.png') }}" alt="Upload Photo" class="img-thumbnail" width="400"> --}}
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('recruiter_id') ? ' has-error' : '' }}">
                <label for="recruiter_id">@lang('Recruiter')</label>
                <select class="form-control select2" id="recruiter_id" name="recruiter_id">
                    <option value="" disabled selected></option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (old('recruiter_id') == $employee->id ?: $employee->id == $account->recruiter_id) ? 'selected': '' }}>{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('recruiter_id'))
                    <span class="help-block"><strong>{{ $errors->first('recruiter_id') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                <label for="manager_id">@lang('Manager')</label>
                <select class="form-control select2" id="manager_id" name="manager_id">
                    <option value="" disabled selected></option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (old('manager_id') == $employee->id ?: $employee->id == $account->manager_id) ? 'selected': '' }}>{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('manager_id'))
                    <span class="help-block"><strong>{{ $errors->first('manager_id') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('practice_id') ? ' has-error' : '' }}">
                <label for="practice_id">@lang('Practice')</label>
                <select class="form-control select2" id="practice_id" name="practice_id">
                    <option value="" disabled selected></option>
                    @foreach ($practices as $practice)
                        <option value="{{ $practice->id }}" {{ (old('practice_id') == $practice->id ?: $practice->id == $account->practice_id) ? 'selected': '' }}>{{ $practice->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('practice_id'))
                    <span class="help-block"><strong>{{ $errors->first('practice_id') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                <label for="division_id">@lang('Division')</label>
                <select class="form-control select2" id="division_id" name="division_id">
                    <option value="" disabled selected></option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" {{ (old('division_id') == $division->id ?: $division->id == $account->division_id) ? 'selected': '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('division_id'))
                    <span class="help-block"><strong>{{ $errors->first('division_id') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                <label for="locality">@lang('City')</label>
                <input type="text" class="form-control" id="locality" name="city" value="{{ old('city') ?: $account->city }}" />
                @if ($errors->has('city'))
                    <span class="help-block"><strong>{{ $errors->first('city') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                <label for="administrative_area_level_1">@lang('State')</label>
                <input type="text" class="form-control" id="administrative_area_level_1" name="state" value="{{ old('state') ?: $account->state }}" />
                @if ($errors->has('state'))
                    <span class="help-block"><strong>{{ $errors->first('state') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" id="street_number" name="number" value="{{ old('number') ?: $account->number }}" />
    <input type="hidden" id="route" name="street" value="{{ old('street') ?: $account->street }}" />
    <input type="hidden" id="country" name="country" value="{{ old('country') ?: $account->country }}" />
    <input type="hidden" id="postal_code" name="zip_code" value="{{ old('zip_code') ?: $account->zip_code }}" />
    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') ?: $account->latitude }}" />
    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') ?: $account->longitude }}" />

    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                <label for="start_date">@lang('Start Date')</label>
                <div class="input-group date datetimepicker">
                    <input type="text" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') ?: $account->start_date ? $account->start_date->format('Y-m-d H:i') : '' }}" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                @if ($errors->has('start_date'))
                    <span class="help-block"><strong>{{ $errors->first('start_date') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6 text-center">
            @if ($action == 'edit' && $account->isRecentlyCreated())
                <div class="bg-success">
                    <br />
                    @lang('Account created in the last')
                    <strong>6</strong>
                    @lang('months').
                    <br />&nbsp;
                </div>
            @endif
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('physicians_needed') ? ' has-error' : '' }}">
                <label for="physicians_needed">@lang('No. of Physicians needed')</label>
                <input type="number" class="form-control" id="physicians_needed" name="physicians_needed" min="0" value="{{ old('physicians_needed') ?: $account->physicians_needed ?: '0' }}" />
                @if ($errors->has('physicians_needed'))
                    <span class="help-block"><strong>{{ $errors->first('physicians_needed') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('apps_needed') ? ' has-error' : '' }}">
                <label for="apps_needed">@lang('No. of APPs needed')</label>
                <input type="number" class="form-control" id="apps_needed" name="apps_needed" min="0" value="{{ old('apps_needed') ?: $account->apps_needed ?: '0' }}" />
                @if ($errors->has('apps_needed'))
                    <span class="help-block"><strong>{{ $errors->first('apps_needed') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('physician_hours_per_month') ? ' has-error' : '' }}">
                <label for="physician_hours_per_month">@lang('No. of hours for Physicians per month')</label>
                <input type="number" class="form-control" id="physician_hours_per_month" name="physician_hours_per_month" min="0" value="{{ old('physician_hours_per_month') ?: $account->physician_hours_per_month ?: '0' }}" />
                @if ($errors->has('physician_hours_per_month'))
                    <span class="help-block"><strong>{{ $errors->first('physician_hours_per_month') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('app_hours_per_month') ? ' has-error' : '' }}">
                <label for="app_hours_per_month">@lang('No. of hours for APP per month')</label>
                <input type="number" class="form-control" id="app_hours_per_month" name="app_hours_per_month" min="0" value="{{ old('app_hours_per_month') ?: $account->app_hours_per_month ?: '0' }}" />
                @if ($errors->has('app_hours_per_month'))
                    <span class="help-block"><strong>{{ $errors->first('app_hours_per_month') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />

    <div class="internal-plan-checkboxes">
        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="press_release" {{ (old('press_release') ?: $account->press_release) ? 'checked' : '' }} />
                        @lang('Has a press release gone out announcing newstart, and if so when?')
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" name="press_release_date" value="{{ old('press_release_date') ?: $account->press_release_date ? $account->press_release_date->format('Y-m-d') : '' }}" placeholder="When?" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="management_change_mailers" {{ (old('management_change_mailers') ?: $account->management_change_mailers) ? 'checked' : '' }} />
                        @lang('Have mailers gone out announcing management change?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruiting_mailers" {{ (old('recruiting_mailers') ?: $account->recruiting_mailers) ? 'checked' : '' }} />
                        @lang('Have mailers gone out for recruiting?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="email_blast" {{ (old('email_blast') ?: $account->email_blast) ? 'checked' : '' }} />
                        @lang('Have email blasts gone out?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="purl_campaign" {{ (old('purl_campaign') ?: $account->purl_campaign) ? 'checked' : '' }} />
                        @lang('PURL Campaign')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="marketing_slick" {{ (old('marketing_slick') ?: $account->marketing_slick) ? 'checked' : '' }} />
                        @lang('Account Marketing slick generated')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="collaboration_recruiting_team" {{ (old('collaboration_recruiting_team') ?: $account->collaboration_recruiting_team) ? 'checked' : '' }} />
                        @lang('Do we need to set up a collaboration recruiting team, and if so, who is on the team?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="collaboration_recruiting_team_names" value="{{ old('collaboration_recruiting_team_names') ?: $account->collaboration_recruiting_team_names }}" placeholder="Who is on the team?" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="compensation_grid" {{ (old('compensation_grid') ?: $account->compensation_grid) ? 'checked' : '' }} />
                        @lang('What is the compensation grid, including sign on bonuses or retention bonuses?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="compensation_grid_bonuses" value="{{ old('compensation_grid_bonuses') ?: $account->compensation_grid_bonuses }}" placeholder="Compensation grid" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruiting_incentives" {{ (old('recruiting_incentives') ?: $account->recruiting_incentives) ? 'checked' : '' }} />
                        @lang('What additional recruiting incentives do we have in place?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="recruiting_incentives_description" value="{{ old('recruiting_incentives_description') ?: $account->recruiting_incentives_description }}" placeholder="Additional recruiting incentives" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="locum_companies_notified" {{ (old('locum_companies_notified') ?: $account->locum_companies_notified) ? 'checked' : '' }} />
                        @lang('Have you notified the locum companies?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="search_firms_notified" {{ (old('search_firms_notified') ?: $account->search_firms_notified) ? 'checked' : '' }} />
                        @lang('Have you notified the 3rd party search firms?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="departments_coordinated" {{ (old('departments_coordinated') ?: $account->departments_coordinated) ? 'checked' : '' }} />
                        @lang('Have you coordinated with the on site hospital marketing department physicians liaisons and internal recruiter?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>
    </div>
    
    @if ($action == 'edit')
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="{{ route('admin.accounts.internalPlan', [$account]) }}" class="btn btn-primary">@lang('Create PDF')</a>
                <br />
                @lang('To give internal plan')
            </div>
        </div>

       {{--  <hr />
        
        <div class="row text-center">
            <div class="col-md-6">
                <a href="javascript:;" class="btn btn-primary">@lang('Print as PDF')</a>
                <br />
                @lang('Marketing Slick')
            </div>
            <div class="col-md-6">
                <a href="javascript:;" class="btn btn-primary">@lang('Email Marketing Slick')</a>
            </div>
        </div> --}}
    @endif

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn {{ $action == 'create' ? 'btn-success' : 'btn-info' }}">
                {{ $action == 'create' ? __('Create') : __('Update') }}
            </button>
        </div>
    </div>

</form>

<div class="modal fade" id="site-code-history" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $account->name }} @lang('Site Code') @lang('History')</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('Site Code')</th>
                                <th>@lang('Modified At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($account->siteCodes as $siteCode)
                                <tr>
                                    <td>{{ $siteCode->site_code }}</td>
                                    <td>{{ $siteCode->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #map {
            height: 300px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function initAutocomplete() {
            var map, marker, mapOptions, markerOptions;
            var lat = Number($('#latitude').val());
            var lng = Number($('#longitude').val());
            // Create the autocomplete object, restricting the search to geographical location types.
            var autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById('google_address')), {
                    types: ['geocode'],
                    componentRestrictions: {country: 'us'}
                }
            );

            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'long_name',
                country: 'long_name',
                postal_code: 'short_name'
            };

            mapOptions = { zoom: 15 };
            mapOptions.center = (lat && lng) ? { lat: lat, lng: lng } : { lat: 42.99092, lng: -71.4682532 };

            // Create a Google Map
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            markerOptions = { map: map };
            markerOptions.position = (lat && lng) ? { lat: lat, lng: lng } : null;

            // Create a Google Marker
            marker = new google.maps.Marker(markerOptions);

            // When the user selects an address from the dropdown, populate the address fields in the form.
            autocomplete.addListener('place_changed', function () {
                fillInAddress(autocomplete, componentForm, map, marker);
            });
        }

        function fillInAddress(autocomplete, componentForm, map, marker) {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            var location = place.geometry.location;

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }

            // Center map and set marker position.
            map.setCenter(location);
            marker.setPosition(location);
            $('#latitude').val(location.lat());
            $('#longitude').val(location.lng());
        }
        /* END Google Address */

        // Image upload
        function updatePathInput(response) {
            $('#photo_path').val(response.path);
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@endpush