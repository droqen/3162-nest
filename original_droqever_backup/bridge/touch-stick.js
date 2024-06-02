
// const joystick = createJoystick(document.getElementById('wrapper')); // setup

// setInterval(() => console.log(joystick.getPosition()), 16); // unused, just a debug thing . . .

function createJoystickAndJoybutton(stickparent, buttonparent, input_cb) {
	function vh(percent) {
		var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
		return (percent * h) / 100;
	}
	  
	  function vw(percent) {
		var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		return (percent * w) / 100;
	}
	  
	function vmin(percent) {
		return Math.min(vh(percent), vw(percent));
	}

	const stickDeadZone = 0; // no dead zone. let the game handle it.
	const maxDiffPercent = 14;
	const maxDiffButtonPercent = 10;

	function getMaxDiff() { return vmin(maxDiffPercent); }
	function getMaxDiffButton() { return vmin(maxDiffButtonPercent); }

	const stick = document.createElement('div');
	stick.classList.add('joystick');
	// const stickGhost = document.createElement('div');
	// stickGhost.classList.add('joyghost');
	// stick.appendChild(stickGhost);

	const button = document.createElement('div');
	button.classList.add('joybutton');
	// const buttonGhost = document.createElement('div');
	// buttonGhost.classList.add('joyghost');
	// button.appendChild(buttonGhost);

	// override gestures
	document.addEventListener("gesturestart", (e)=>{ e.preventDefault(); document.body.style.zoom = 0.99; document.body.style.zoom = 1; });
	document.addEventListener("gesturechange", (e)=>{ e.preventDefault(); document.body.style.zoom = 0.99; document.body.style.zoom = 1; });
	document.addEventListener("gestureend", (e)=>{ e.preventDefault(); document.body.style.zoom = 1; });

	document.addEventListener('mousedown', handleMouseDown);
	document.addEventListener('mousemove', handleMouseMove);
	document.addEventListener('mouseup', handleMouseUp);
	document.addEventListener('touchstart', handleMouseDown, {passive:false});
	document.addEventListener('touchmove', handleMouseMove, {passive:false});
	document.addEventListener('touchend', handleMouseUp, {passive:false});
	document.addEventListener('touchcancel', handleMouseUp, {passive:false});

	let canvas = document.getElementById("canvas");
	canvas.classList = "never_unfocus";

	let stickTouchID = null;
	let dragStart = null;
	let buttonTouchID = null;
	let buttHoldStart = null;

	let currentPos = { x: 0, y: 0 };
	let currentButtonHeld = false;

	let cbChangedBool = false;

	// use interval (60+FPS) instead of constant callback, the constant callback is causing lag.
	setInterval(()=>{
		if (cbChangedBool) {
			cbChangedBool = false;
			const maxDiff = getMaxDiff();
			input_cb(currentPos.x / maxDiff, currentPos.y / maxDiff, currentButtonHeld);
			stick.style.transition = '0s';
			stick.style.transform = `translate3d(${currentPos.x}px, ${currentPos.y}px, 0px)`;
		}
	}, 15);

	function cbChanged() {
		cbChangedBool = true;
		// // todo: add deadzone
		// const maxDiff = getMaxDiff();
		// input_cb(currentPos.x / maxDiff, currentPos.y / maxDiff, currentButtonHeld);
	}

	function handleMouseDown(event) {
		if (event.changedTouches) {
			for (let i = 0; i < event.changedTouches.length; i++) {
				var array = ["stick", "button"];
				if (event.changedTouches[i].clientX > window.innerWidth * 0.5) {
					array = ["button", "stick"];
				}
				let touchHandled = false;
				array.forEach(element => {
					if (touchHandled) {
						return;
					} else if (element == "stick") {
						if (stickTouchID == null && event.changedTouches[i].clientX < window.innerWidth * 0.65) {
							stickTouchID = event.changedTouches[i].identifier;
							dragStart = {
								x: event.changedTouches[i].clientX,
								y: event.changedTouches[i].clientY,
							};
							stickparent.classList = "active";
							stickparent.style.left = (dragStart.x).toString() + "px";
							stickparent.style.top = (dragStart.y).toString() + "px";
							stick.style.transform = ``;
							cbChanged();
							touchHandled = true;
						}
					} else if (element == "button") {
						if (buttonTouchID == null) {
							buttonTouchID = event.changedTouches[i].identifier;
							buttHoldStart = {
								x: event.changedTouches[i].clientX,
								y: event.changedTouches[i].clientY,
							}
							buttonparent.classList = "active";
							buttonparent.style.left = (buttHoldStart.x).toString() + "px";
							buttonparent.style.top = (buttHoldStart.y).toString() + "px";
							button.style.transform = ``;
							currentButtonHeld = true;
							cbChanged();
							touchHandled = true;
							event.preventDefault();
						}
					}
				});
			}
		}
		// else {
		// 	dragStart = {
		// 		x: event.clientX,
		// 		y: event.clientY,
		// 	};
		// }
	}

	function handleMouseMove(event) {
		// event.preventDefault();
		if (event.changedTouches) {
			for (let i = 0; i < event.changedTouches.length; i++) {
				if (event.changedTouches[i].identifier == stickTouchID) {
					const xDiff = event.changedTouches[i].clientX - dragStart.x;
					const yDiff = event.changedTouches[i].clientY - dragStart.y;
					const angle = Math.atan2(yDiff, xDiff);
						const maxDiff = getMaxDiff();
						const distance = Math.min(maxDiff, Math.hypot(xDiff, yDiff));
						const xNew = distance * Math.cos(angle);
						const yNew = distance * Math.sin(angle);
					currentPos = { x: xNew, y: yNew };
					cbChanged();
				}
				if (event.changedTouches[i].identifier == buttonTouchID) {
					const xDiff = event.changedTouches[i].clientX - buttHoldStart.x;
					const yDiff = event.changedTouches[i].clientY - buttHoldStart.y;
					const angle = Math.atan2(yDiff, xDiff);
						const distance = Math.hypot(xDiff, yDiff);
						const maxDiffButton = getMaxDiffButton();
						if (distance >= maxDiffButton) {
							buttonTouchID = null; // released
							buttonparent.classList = "";
							button.style.transform = ``;
							button.style.opacity = ``;
							currentButtonHeld = false;
							cbChanged();
						} else {
							const xNew = distance * Math.cos(angle);
							const yNew = distance * Math.sin(angle);
							button.style.transform = `translate3d(${xNew}px, ${yNew}px, 0px);`; // hmm weird that this doesnt do anything
							button.style.opacity = 1.5 - 1.25 * (distance / maxDiffButton);
						}
				}
			}
		}
	}

	function handleMouseUp(event) {
		if (event.changedTouches === undefined) {
			// cancel all touches
			stickTouchID = null;
			stickparent.classList = "";
			stick.style.transition = '.2s';
			stick.style.transform = `translate3d(0px, 0px, 0px)`;
			dragStart = null;
			currentPos = { x: 0, y: 0 };
			buttonTouchID = null;
			buttonparent.classList = "";
			button.style.transform = ``;
			button.style.opacity = ``;
			currentButtonHeld = false;
			cbChanged();
		} else if (event.changedTouches) {
			 for (let i = 0; i < event.changedTouches.length; i++) {
				if (event.changedTouches[i].identifier == stickTouchID) {
					stickTouchID = null;
					stickparent.classList = "";
					stick.style.transition = '.2s';
					stick.style.transform = `translate3d(0px, 0px, 0px)`;
					dragStart = null;
					currentPos = { x: 0, y: 0 };
					cbChanged();
				}
				if (event.changedTouches[i].identifier == buttonTouchID) {
					buttonTouchID = null;
					buttonparent.classList = "";
					button.style.transform = ``;
					button.style.opacity = ``;
					currentButtonHeld = false;
					cbChanged();
				}
			}
		}
	}

	stickparent.appendChild(stick);
	buttonparent.appendChild(button);

	return 0; // OK
	// return {
	// 	getPosition: () => currentPos,
	// };
}