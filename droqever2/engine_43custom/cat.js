Cat = (function () {
	const statusOverlay = document.getElementById('status');
	const statusProgress = document.getElementById('status-progress');
	const statusNotice = document.getElementById('status-notice');

	let initializing = true;
	let loading = false;
	let statusMode = '';

	function setStatusMode(mode) {
		if (statusMode === mode) {
			return;
		}
		if (mode === 'hidden') {
			statusOverlay.style.visibility = 'hidden';
			initializing = false;
			loading = false;
			return;
		}
		statusOverlay.style.visibility = 'visible';
		statusProgress.style.display = mode === 'progress' ? 'block' : 'none';
		statusNotice.style.display = mode === 'notice' ? 'block' : 'none';
		statusMode = mode;
	}

	function setStatusNotice(text) {
		while (statusNotice.lastChild) {
			statusNotice.removeChild(statusNotice.lastChild);
		}
		const lines = text.split('\n');
		lines.forEach((line) => {
			statusNotice.appendChild(document.createTextNode(line));
			statusNotice.appendChild(document.createElement('br'));
		});
	}

	function displayFailureNotice(err) {
		const msg = err.message || err;
		console.error(msg);
		setStatusNotice(msg);
		setStatusMode('notice');
		initializing = false;
		loading = false;
	}

	var try_start_game = function(game_path, game_filesize = 0) {
		if (loading) {
			return false;
		} else {
			console.log("set status mode progress");
			setStatusMode('progress');
			engine.startGame({
				'args': [],
				'mainPack': game_path,
				'fileSizes':{game_path:game_filesize,"/engine_43custom/godot.wasm":46560934},
				'onProgress': function (current, total) {
					if (current > 0 && total > 0) {
						statusProgress.value = current;
						statusProgress.max = total;
					} else {
						statusProgress.removeAttribute('value');
						statusProgress.removeAttribute('max');
					}
				},
			}).then(() => {
				console.log("set status mode hidden");
				setStatusMode('hidden');
				let event = new Event('canvasGameLoaded', {});
				window.dispatchEvent(event);
			}, displayFailureNotice);
			return true;
		}
	}
	const GODOT_CONFIG = {
		"args":[],
		"canvasResizePolicy":0, // default 2, but in my case - i want to handle it manually. see 'resize' event below.
		"ensureCrossOriginIsolationHeaders":true,
		"executable":"/engine_43custom/godot",
		"mainPack":"/games_43custom/void.zip",
		"experimentalVK":false,
		"focusCanvas":true,
		"unloadAfterInit":false,
		"gdextensionLibs":[]};
	const GODOT_THREADS_ENABLED = false;
	const engine = new Engine(GODOT_CONFIG);

	const missing = Engine.getMissingFeatures({
		threads: GODOT_THREADS_ENABLED,
	});

	let booted = false;

	var boot = function(first_game_path = '/games_43custom/void.zip', first_game_filesize = 8388) {
		if (booted) { console.log("don't boot me twice"); return; }
		booted = true;
		if (missing.length !== 0) {
			if (GODOT_CONFIG['serviceWorker'] && GODOT_CONFIG['ensureCrossOriginIsolationHeaders'] && 'serviceWorker' in navigator) {
				// There's a chance that installing the service worker would fix the issue
				Promise.race([
					navigator.serviceWorker.getRegistration().then((registration) => {
						if (registration != null) {
							return Promise.reject(new Error('Service worker already exists.'));
						}
						return registration;
					}).then(() => engine.installServiceWorker()),
					// For some reason, `getRegistration()` can stall
					new Promise((resolve) => {
						setTimeout(() => resolve(), 2000);
					}),
				]).catch((err) => {
					console.error('Error while registering service worker:', err);
				}).then(() => {
					window.location.reload();
				});
			} else {
				// Display the message as usual
				const missingMsg = 'Error\nThe following features required to run Godot projects on the Web are missing:\n';
				displayFailureNotice(missingMsg + missing.join('\n'));
			}
		} else if (try_start_game(first_game_path, first_game_filesize)) {
			// good!
		} else {
			console.log("FAILED STARTING MY FIRST GAME >:( big problem");
		}
	}

	var set_game_size = function(w,h) {
		let setGameSizeEvent = new Event("setGameSize", {});
		setGameSizeEvent['w'] = w;
		setGameSizeEvent['h'] = h;
		
		window.dispatchEvent(setGameSizeEvent);
	}

	var on_windfish_awakened = function() {
		let wfAwakenedEvent = new Event("wfAwakened", {});
		console.log("WINDFISH AWAKENED. <this should only be sent 1x>"); // todo - only send this once
		window.dispatchEvent(wfAwakenedEvent);
	}

	return {
		boot : boot,
		try_start_game : try_start_game,
		set_game_size : set_game_size,
		on_windfish_awakened : on_windfish_awakened,
	}
}());

if (typeof window !== 'undefined') {
	window['Cat'] = Cat;
	// console.log("it must be done. is Cat in window?")
	// console.log(window['Cat'])
} else {
	console.log("window not found, could not put Cat in window :(");
}