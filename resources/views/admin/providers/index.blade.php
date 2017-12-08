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
	<div class="box-body providers-page">
		@if($sites->count() > 0)
			<div class="site title">
				<div class="name title">Hospital</div>
				<div class="stage title">Stage 1</div>
				<div class="stage title">Stage 2</div>
				<div class="stage title">Stage 3</div>
			</div>
		@endif
		@foreach($sites as $key => $site)
			<div class="site">
				<div class="name">
					{{$key}}
				</div>
				@foreach($site as $stage_key => $stage)
					<div class="stage stage{{$stage_key}}">
						@foreach($stage as $providers)
							@if($providers->count() > 15)
								<div class="draggable" data-providers="{{$providers}}">
									Phy {{$providers->count()}}
								</div>
							@else
								@foreach($providers as $provider_key => $provider)
									<div data-info="{{$provider}}" class="draggable" data-provider="{{$provider->name}}" 
										data-account="{{$provider->pipeline->account->name}}">
										P {{substr($provider->name, 0, 3)}}
									</div>
								@endforeach
							@endif
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
			items: "[data-provider], [data-account], [data-providers], [data-info]",
			content: function() {
				var element = $( this );

				if (element.is("[data-provider]")) {
					var provider = element.data('provider');
					var account = element.data('account');

					var element = '<div>'+provider+'<br><br>'+account+'</div>';
				} else if (element.is("[data-providers]")) {
					var providers = element.data('providers');

					var element = '<div><table class="table table-bordered table-striped"><tr><td>Physician</td><td>Hospitals</td></tr>';
					$.each(providers, function(index, provider) {
						element += '<tr><td>'+provider.name+'</td><td>'+provider.pipeline.account.name+'</td></tr>'
					});
					element += '</table></div>';
				}
				
				return element;
			}
		});
		$(document).ready(function() {

			var postUrl = '/admin/providers/switch';

			function sort(element) {
				var items = element.children().sort(function(a, b) {
			        var vA = $(a).text();
			        var vB = $(b).text();
			        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
			    });

    			element.append(items);
			}

			$(".draggable").draggable({ axis: "x", revert: "invalid" });

			$(".stage1").droppable({ accept: ".draggable", 
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var info = dropped.data('info');

					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

					sort(droppedOn);

					$.post(postUrl, {_token: "{{csrf_token()}}", data: info}, function(response) {
						console.log(response);
					});
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
					var info = dropped.data('info');

					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

					sort(droppedOn);

					$.post(postUrl, {_token: "{{csrf_token()}}", data: info}, function(response) {
						console.log(response);
					});
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
					var info = dropped.data('info');

					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

					sort(droppedOn);

					$.post(postUrl, {_token: "{{csrf_token()}}", data: info}, function(response) {
						console.log(response);
					});
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});
		});
	</script>
@endpush