const planningSelect = document.getElementById('planningSelect');

planningSelect.addEventListener('change', (e) => {
	window.location.href = "https://family-re7.herokuapp.com/shopping/" + e.target.value;
});