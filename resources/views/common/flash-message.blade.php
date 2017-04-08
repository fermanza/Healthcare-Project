@if (session('flash-message'))
    <script>
        $(document).ready(function () {
            toastr['{{ session('flash-message.type') }}']('{{ session('flash-message.message') }}', null, {
                progressBar: true,
                positionClass: 'toast-top-center'
            });
        });
    </script>
@endif