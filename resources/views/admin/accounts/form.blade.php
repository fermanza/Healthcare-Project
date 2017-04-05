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
            <img src="{{ asset('img/user2-160x160.jpg') }}" alt="Upload Photo" class="img-thumbnail" width="300">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="google_address">@lang('Address')</label>
                <input type="text" class="form-control" id="google_address">
            </div>
            <div>
                {{-- <div id="map"></div> --}}
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
                <label for="locality">@lang('City')</label>
                <input type="text" class="form-control" id="locality" name="city" placeholder="Autopopulate City" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="administrative_area_level_1">@lang('State')</label>
                <input type="text" class="form-control" id="administrative_area_level_1" name="state" placeholder="Autopopulate State" />
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

    <input type="hidden" id="street_number" />
    <input type="hidden" id="route" />
    <input type="hidden" id="country" />
    <input type="hidden" id="postal_code" />

</form>

@push('styles')
    <style>
        #map {
            height: 500px;
        }
    </style>
@endpush

@push('scripts')
    <script>
      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('google_address')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6c4prh5LPl0vynfoez7XFNOpE1IekV6g&libraries=places&callback=initAutocomplete" async defer></script>
@endpush