@if(session('token'))
<div class="alert alert-success">
    <p>
        We've created your new project!
        <strong>Note, that we won't show you this token again.</strong>
    </p>

    Here's your access token:
    <code>
        {{ session('token') }}
    </code>
</div>
@endif
