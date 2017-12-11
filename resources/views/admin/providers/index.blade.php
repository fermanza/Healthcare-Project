@extends('layouts.admin')

@section('content-header', __('Provider Dashboard'))

@section('content')
<div class="providers-page">
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
	<div class="box-body">
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
								<div class="draggable multiple" data-providers="{{$providers}}">
									Phy {{$providers->count()}}
								</div>
							@else
								@foreach($providers as $provider_key => $provider)
									<div class="draggable single stage{{$stage_key}}" data-provider="{{$provider->name}}" data-info="{{$provider}}" data-accounts="{{$provider->provider ? $provider->provider->accounts : ''}}">
										@if($provider->type == 'phys') 
											P {{substr($provider->name, 0, 3)}}
										@else
											A {{substr($provider->name, 0, 3)}}
										@endif
									</div>
								@endforeach
							@endif
						@endforeach
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
	<button onclick="revertDrop()">revert</button>
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span id="providerName"></span>
                    </h4>
                </div>
                <div class="modal-body">
                	<label>Provider Accounts</label>
                	<ul id="providerAccounts">
                		
                	</ul>
                	<label>Add Accounts To This Provider</label>
                    <select class="form-control select2" name="accounts[]" data-placeholder="@lang('Account')" multiple>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                	<button type="button" class="btn btn-success">Confirm</button>
			    	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
	<script>
		$( document ).tooltip({
			items: "[data-provider], [data-accounts], [data-providers], [data-info]",
			content: function() {
				var element = $( this );

				if (element.is("[data-provider]")) {
					var provider = element.data('provider');
					var accounts = element.data('accounts');

					var element = '<div class="bold-text">'+provider+'</div>';
					var accountsList = '';
					$.each(accounts, function(index, account) {
						accountsList += account.name+'; ';
					});
					element += '<div><p>'+accountsList+'</p></div>';
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
			var droppedElement = null;
			var droppedFrom = null;
			var oldClass = null;
			var newClass = null;

			function sort(element) {
				var items = element.children().sort(function(a, b) {
			        var vA = $(a).text().toLowerCase();
			        var vB = $(b).text().toLowerCase();
			        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
			    });

    			element.append(items);
			}

			$(".draggable").draggable({ axis: "x", revert: "invalid" });

			$(".draggable.single").dblclick(function() {
				var provider = $(this);
				var providerName = provider.data('provider');
				$('#providerName').text(providerName);

				var providerAccounts = provider.data('accounts');
				var accountsList = '';
				$.each(providerAccounts, function(index, account) {
					accountsList += '<li>'+account.name+'</li>';
				});
				$('#providerAccounts').append(accountsList);

				$('#editModal').modal('toggle');
			});

			$(".stage1").droppable({ accept: "",
				create: function(event, ui) {
					sort($(this));
				},
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

			$(".stage2").droppable({ accept: ".draggable.stage1",
				create: function(event, ui) {
					sort($(this));
				},
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var info = dropped.data('info');

					oldClass = 'stage1';
					newClass = 'stage2';

					dropped.removeClass(oldClass);
					dropped.addClass(newClass);

					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

					sort(droppedOn);

					$.post(postUrl, {_token: "{{csrf_token()}}", data: info}, function(response) {
						console.log(response);
					});

					droppedElement = dropped;
					droppedFrom = $('.stage.stage1');
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$(".stage3").droppable({ accept: ".draggable.stage2",
				create: function(event, ui) {
					sort($(this));
				},
				drop: function(event, ui) {
					$(this).removeClass("border").removeClass("over");
					var dropped = ui.draggable;
					var info = dropped.data('info');

					oldClass = 'stage2';
					newClass = 'stage3';

					var droppedOn = $(this);
					$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

					sort(droppedOn);

					$.post(postUrl, {_token: "{{csrf_token()}}", data: info}, function(response) {
						console.log(response);
					});

					droppedElement = dropped;
					droppedFrom = $('.stage.stage2');
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$('#editModal').on('hidden.bs.modal', function () {
			    $('#providerAccounts').html('');
			})

			window.revertDrop = function() {
				droppedElement.removeClass(newClass);
				droppedElement.addClass(oldClass);

				$(droppedElement).detach().css({top: 0,left: 0}).appendTo(droppedFrom);
				sort(droppedFrom);
			}
		});
	</script>
@endpush