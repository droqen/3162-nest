
function getRandomPastel() {
	var letters = 'ABCDEF'; // '0123456789ABCDEF';
	var numletters = 6; // 16
	var color = '#';
	for (var i = 0; i < 6; i++) {
		color += letters[Math.floor(Math.random() * numletters)];
	}
	return color;
}

document.body.style.backgroundColor = getRandomPastel();

setTimeout(() => {
	document.body.style.backgroundColor = getRandomPastel();
	setInterval(() => {
		document.body.style.backgroundColor = getRandomPastel();
	}, 10000);
}, 1);

var statusProgressEl = document.getElementById("status-progress");
if (statusProgressEl) {
	statusProgressEl.style.accentColor = getRandomPastel();
}