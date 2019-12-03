<!DOCTYPE html>
<html>

@push('css')
	<style>
		.login-box {width:460px !important;}
	</style>
@endpush
@include('partials.head')

<body class="hold-transition login-page">

<div class="login-box col-md-12">
	<div class="card">
		<div class="card-header">Login</div>
		<div class="card-body">
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Whoops!</strong> Terjadi kesalahan.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			<form role="form" method="POST" action="/login">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<div class="form-group row">
					<label class="col-md-4 col-form-label">Username</label>
					<div class="col-md-8">
						<input type="text" class="form-control" name="username" value="{{ old('username') }}">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-md-4 col-form-label">Password</label>
					<div class="col-md-8">
						<input type="password" class="form-control" name="password">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12 col-md-offset-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="remember"> Remember Me
							</label>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12 col-md-offset-4">
						<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
							Login
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@include('partials.scripts')

</body>

</html>