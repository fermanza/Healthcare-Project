@extends('layouts.admin')

@section('content-header', __('Provider Dashboard'))

@section('content')
	<form class="box-body">
        <div class="flexboxgrid">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="sites[]" data-placeholder="@lang('Site Code')" multiple>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ in_array($account->id, Request::input('sites') ?: []) ? 'selected' : '' }}>
                                {{ $account->siteCode }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fa fa-filter"></i>
                        @lang('Apply')
                    </button>
                    <a href="{{ route('admin.providers.index') }}" type="submit" class="btn btn-sm btn-default">
                        <i class="fa fa-times"></i>
                        @lang('Clear')
                    </a>
                </div>
            </div>
        </div>
    </form>
	<div class="providers">
		@foreach($sites as $key => $site)
			<div>
				<div class="name">
					{{$key}}
				</div>
				@foreach($site as $stage_key => $stage)
					<div class="stage stage{{$stage_key}}">
						@foreach($stage as $provider_key => $provider)
							<div data-id="{{$provider->id}}" class="draggable" title="{{$provider->name}}">
								Phy {{$provider_key}}
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
@endsection

@push('scripts')
	<script>
		$( document ).tooltip({
			items: "[data-text]",
			content: function() {
				var element = $( this );
				if ( element.is( "[data-text]" ) ) {
					var text = element.text();
					return "<img class='map' alt='" + text +
					"' src='http://maps.google.com/maps/api/staticmap?" +
					"zoom=11&size=350x350&maptype=terrain&sensor=false&center=" +
					text + "'>";
				}
			}
		});
		$(document).ready(function() {

			$(".draggable").draggable({ axis: "x", revert: "invalid" });

			$(".stage1").droppable({ accept: ".draggable", 
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);      
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$(".stage2").droppable({ accept: ".draggable", 
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);      
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$(".stage3").droppable({ accept: ".draggable", 
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);      
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$(".stage1, .stage2, .stage3").sortable();
		});
	</script>
@endpush