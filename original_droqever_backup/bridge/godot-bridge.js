Bridge = {
	setup_joystick: function(joystick_input_cb) {
		console.log("setup . . .");
		const wrapper1 = document.getElementById('touch_stick_wrapper');
		const wrapper2 = document.getElementById('touch_button_wrapper');
		if (wrapper1 && wrapper2) {
			return createJoystickAndJoybutton(wrapper1, wrapper2, joystick_input_cb); // setup
		} else {
			return 535; // "stick wrapper not found"
		}
	},
	setup_joystick_if_mobile: function(joystick_input_cb) {
		console.log("setup if mobile . . .");
		if (( 'ontouchstart' in window ) || 
		( navigator.maxTouchPoints > 0 ) || 
		( navigator.msMaxTouchPoints > 0 )) {
			return Bridge.setup_joystick(joystick_input_cb);
		} else {
			return 507; // "not mobile"
		}
	},
	report_connection: function() {
		console.log("successfully called Bridge.report_connection (gd called a js function)");
	}
}