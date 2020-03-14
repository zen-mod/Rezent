@if(session('revoked') == true)
<div class="alert alert-success">
    <p>We've removed your project!</p>
    Note, anyone using the project's assigned credentials will have their access revoked.
</div>
@else
<div class="alert-alert-danger">
    <p><strong>Whoops!</strong> Something went wrong!</p>
    We were unable to remove your token. Try again later.
</div>
@endif
