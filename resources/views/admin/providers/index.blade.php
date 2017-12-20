@extends('layouts.admin')

@section('content-header', __('Provider Dashboard'))

@section('content')
<div id="providersPage" class="providers-page">
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
				<div class="name title">Hospital</div><div class="stage title">Stage 1</div><div class="stage title">Stage 2</div><div class="stage title">Stage 3</div>
			</div>
		@endif
		<div v-for="(site, siteIndex) in sites" class="site" v-cloak>
			<div class="name">
				@{{siteIndex}}
			</div><div v-for="(stage, stageIndex) in site" class="stage" :class="'stage'+stageIndex">
				<div v-if="stage[0].length <= 15" v-for="(provider, providerIndex) in stage[0]" class="draggable single" :class="'stage'+stageIndex" :data-info="convertJson(provider)" @dblclick="setSite(provider)">
					P @{{ cutName(provider.name) }}
				</div><div v-if="stage[0].length > 15" class="draggable" :class="'stage'+stageIndex" :data-providers="convertJson(stage[0])" @dblclick="setProviders(stage[0])">
					@{{stage[0].length}}
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            	<form @submit.prevent="addHospitals(extraAccounts)">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                    <h4 class="modal-title">
	                        <span class="provider-name">@{{ site.name }}</span>
	                    </h4>
	                </div>
	                <div class="modal-body">
	                	<label>Provider Accounts</label>
	                	<ul>
	                		<li v-for="account in site.provider.accounts">@{{account.name}}</li>
	                	</ul>
	                	<label>Add Accounts To This Provider</label>
	                    <select id="additionalAccounts" class="form-control select2" name="accounts[]" data-placeholder="@lang('Account')" multiple v-model="extraAccounts" required>
	                        @foreach ($accounts as $account)
	                            <option value="{{ $account->id }}">
	                                {{ $account->name }}
	                            </option>
	                        @endforeach
	                    </select>
	                </div>
	                <div class="modal-footer">
	                	<button type="submit" class="btn btn-success">Confirm</button>
				    	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				    </div>
				</form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="moveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            	<form id="moveProvider">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                    <h4 class="modal-title">
	                        <span class="provider-name"></span>
	                    </h4>
	                </div>
	                <div class="modal-body">
	                	<label>Projected Start Date</label>
	                    <input type="text" class="form-control datepicker-future" id="projectedStartDate" required />

	                    <label>Contract In</label>
	                    <input type="text" class="form-control datepicker" id="contractIn" required />
	                </div>
	                <div class="modal-footer">
	                	<button type="submit" class="btn btn-success">Confirm</button>
				    	<button onclick="revertDrop()" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				    </div>
				</form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="providersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            	<form @submit.prevent="addProvidersHospitals(providersProvider, providersHospitals)">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                    <h4 class="modal-title">
	                        Providers
	                    </h4>
	                </div>
	                <div class="modal-body">
	                	<table class="table table-striped">
	                		<tr>
	                			<th>Provider</th>
	                			<th>Hospitals</th>
	                		</tr>
	                		<tr v-for="provider in providers">
	                			<td>@{{provider.name}}</td>
	                			<td v-if="provider.provider">
	                				<span v-for="hospital in provider.provider.accounts">
	                					@{{hospital.name}};
	                				</span>
	                			</td>
	                			<td v-if="!provider.provider"></td>
	                		</tr>
	                	</table>
	                	<div>
	                		<label>Add hospitals to providers</label>
	                		<div>
		                		<select id="providersProvider" class="form-control select2" data-placeholder="@lang('Provider')" v-model="providersProvider" required>
		                			<option value=""></option>
		                			<option v-for="provider in providers" :value="provider.id">@{{provider.name}}</option>
		                		</select>
		                	</div>
		                	<div class="mt10">
	                			<select id="providersHospitals" class="form-control select2" data-placeholder="@lang('Hospitals')" multiple v-model="providersHospitals" required>
	                				@foreach ($accounts as $account)
			                            <option value="{{ $account->id }}">
			                                {{ $account->name }}
			                            </option>
			                        @endforeach
	                			</select>
	                		</div>
	                	</div>
	                </div>
	                <div class="modal-footer">
	                	<button type="submit" class="btn btn-success">Confirm</button>
				    	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				    </div>
				</form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
	<script>
		$( document ).tooltip({
			items: "[data-providers], [data-info]",
			content: function() {
				var element = $( this );

				if (element.is("[data-info]")) {
					var provider = element.data('info');
					var accounts = provider.provider && provider.provider.accounts ? provider.provider.accounts : [];

					var element = '<div class="bold-text">'+provider.name+'</div>';
					var accountsList = '';
					$.each(accounts, function(index, account) {
						accountsList += account.name+'; ';
					});
					element += '<div><p>'+accountsList+'</p></div>';
				} else if (element.is("[data-providers]")) {
					var providers = element.data('providers');

					var element = '<div><table class="table fs10 table-bordered table-striped"><tr><td>Physician</td><td>Hospitals</td></tr>';
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
			var stage = null;

			function sort(element) {
				var items = element.children().sort(function(a, b) {
			        var vA = $(a).text().toLowerCase();
			        var vB = $(b).text().toLowerCase();
			        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
			    });

    			element.append(items);
			}

			$('#moveProvider').on('submit', function(e) {
				e.preventDefault();
				var provider = droppedElement.data('info');
				var firstShift = $('#projectedStartDate').val();
				var contractIn = $('#contractIn').val();

				if(provider.contractOut) {
					provider.contractOut = moment(provider.contractOut).format('MM/DD/YYYY');
				}
				provider.firstShift = moment(firstShift).format('MM/DD/YYYY');
				provider.contractIn = moment(contractIn).format('MM/DD/YYYY');
				if(provider.interview) {
					provider.interview = moment(provider.interview).format('MM/DD/YYYY');
				}

				$.post(postUrl, {_token: "{{csrf_token()}}", provider: provider, stage: stage}, function(response) {
					
				});

				$('#moveModal').modal('toggle');
			});

			$(".draggable").draggable({ axis: "x", revert: "invalid" });

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

					var provider = dropped;
					var providerInfo = provider.data('info');
					$('.provider-name').text(providerInfo.name);

					$('#moveModal').modal('toggle');

					droppedElement = dropped;
					droppedFrom = $('.stage.stage1');
					stage = 2;
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

					droppedElement = dropped;
					droppedFrom = $('.stage.stage2');
					stage = 3;

					$.post(postUrl, {_token: "{{csrf_token()}}", provider: info, stage: stage}, function(response) {
						
					});
				},
				over: function(event, elem) {
					$(this).addClass("over");
				},
				out: function(event, elem) {
					$(this).removeClass("over");
				}
			});

			$('#providersModal').on('hidden.bs.modal', function () {
			    $("#providersProvider.select2").val(null).trigger('change');
			    $("#providersHospitals.select2").val(null).trigger('change');
			    window.providersApp.resetProviders();
			});

			$('#editModal').on('hidden.bs.modal', function () {
			    $("#additionalAccounts.select2").val(null).trigger('change');
			    window.providersApp.resetExtraAccounts();
			});

			$('#moveModal').on('hidden.bs.modal', function () {
			    $('#projectedStartDate').val('');
			    $('#contractIn').val('');
			});

			window.revertDrop = function() {
				droppedElement.removeClass(newClass);
				droppedElement.addClass(oldClass);

				$(droppedElement).detach().css({top: 0,left: 0}).appendTo(droppedFrom);
				sort(droppedFrom);
			}
		});

		window.providersApp = new Vue({
            el: '#providersPage',

            data: {
            	sites: BackendVars.sites,
            	accounts: BackendVars.accounts,
            	site: {
            		name: '',
            		provider: {
            			id: null,
            			accounts: []
            		},
            	},
            	extraAccounts: [],
            	providersProvider: null,
            	providersHospitals: [],
            	providers: {}
            },

            methods: {
            	setSite: function(site) {
            		this.site = site;
            		this.site.provider = this.site.provider == null ? {id: null, accounts: []} : this.site.provider;

            		$('#editModal').modal('toggle');
            	},
            	setProviders: function(providers) {
            		this.providers = providers;

            		$('#providersModal').modal('toggle');
            	},
            	cutName: function(name) {
            		return name.substring(0, 3);
            	},
            	convertJson: function(object) {
            		return JSON.stringify(object);
            	},
            	addHospitals: function(accounts) {
            		axios.post('/admin/providers/addHospitals', {providerId: this.site.provider.id, hospitals: this.extraAccounts})
                        .then(function (response) {
                        	if(typeof response.data == 'string') {
                        		alert('Not linked to any provider');
                        	} else {
                            	this.site.provider = response.data;
                            }

                            $('#editModal').modal('toggle');
                        }.bind(this));
            	},
            	addProvidersHospitals: function(provider, hospitals) {
            		var providerInfo = _.find(this.providers, {id: provider});
            		providerInfo.provider = providerInfo.provider == null ? {id: null, accounts: []} : providerInfo.provider;

            		axios.post('/admin/providers/addHospitals', {providerId: providerInfo.provider.id, hospitals: hospitals})
                        .then(function (response) {
                        	if(typeof response.data == 'string') {
                        		alert('Not linked to any provider');
                        	} else {
                                _.assignIn(providerInfo.provider, response.data);
                            }

                            $('#providersModal').modal('toggle');
                        }.bind(this));
            	},
            	resetExtraAccounts: function() {
            		this.extraAccounts = [];
            	},
            	resetProviders: function() {
            		this.providersProvider = null;
            		this.providersHospitals = [];
            	}
            }
        });
	</script>
@endpush