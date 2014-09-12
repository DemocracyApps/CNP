<div class="menu">
	--<br/>
	<div class="menu-inner">
		<ul class="menu">
			<li class="menu"><a href="/stories">Stories</a></li>
			@if (\Auth::check())
				<li class="menu"><a href="/account">Account</a></li>
				<li class="menu"><a href="/relationtypes">Relations</a></li>
				<li class="menu"><a href="/logout">Log Out</a></li>
			@else
				<li class="menu"><a href="http://cnp.dev/login">Log In</a></li>
			@endif
		</ul>
	</div>
</div><div>

</div>
