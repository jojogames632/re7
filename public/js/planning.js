const planningSelect = document.getElementById('planningSelect');
const planningContainer = document.getElementById('planningContainer');
const Url = new URL(window.location.href);

planningSelect.addEventListener('change', (e) => {

	fetch(Url.pathname + "?owner=" + e.target.value + "&ajax=1", {
		headers: {
			'X-Requested-Width': 'XMLHttpRequest'
		}
	}).then(response => 
		response.json()
	).then(data => {
		planningContainer.innerHTML = data.content;
	}).catch(e => alert(e));

})

