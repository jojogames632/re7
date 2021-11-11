const planningSelect = document.getElementById('planningSelect');

planningSelect.addEventListener('change', (e) => {
	window.location.href = "http://symfony.localhost/shopping/" + e.target.value;
});