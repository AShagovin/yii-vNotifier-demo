<h1>Notifications</h1>
<div id="notification-area">
	<ul data-bind="foreach : {data: notifications, beforeRemove: beforeRemove, afterAdd : afterAdd }">
		<li data-bind="text: message"></li>
	</ul>
</div>

