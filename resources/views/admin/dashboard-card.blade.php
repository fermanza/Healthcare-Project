@permission($route)
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
        <a href="{{ route($route) }}" class="small-box bg-aqua">
            <div class="inner">
                <h3>@lang($resource)</h3>
                <p>&nbsp;</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw {{ $icon }}"></i>
            </div>
        </a>
    </div>
@endpermission
