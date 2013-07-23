vn.initKONotification = function() {

	// notification area
	var notificationArea = document.getElementById('notification-area'),
	// KnockoutJS View Model
	viewModel = {
		notifications : ko.observableArray(),
		beforeRemove : function(el) {
			if(el.nodeType === 1) {
				$(el).fadeOut(400);
			}
		},
		afterAdd : function(el) {
			$(el).hide().fadeIn(400);
		}
	};

	ko.applyBindings(viewModel,notificationArea);

	vn.NotificationHandlers.__default__ = function(notification) {
		viewModel.notifications.push({message : notification});

		setTimeout(function() {
			viewModel.notifications.shift();
		}, 5000);
	};
};