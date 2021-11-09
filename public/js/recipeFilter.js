let recipeTitleInput = document.getElementById('recipeTitle');
const recipesSection = document.getElementById('recipesSection');

recipeTitleInput.addEventListener('input', (e) => { 
	const Url = new URL(window.location.href);

	fetch(Url.pathname + "?title=" + e.target.value + "&ajax=1", {
		headers: {
			'X-Requested-Width': 'XMLHttpRequest'
		}
	}).then(response => 
		response.json()
	).then(data => {
		recipesSection.innerHTML = data.content;
	}).catch(e => alert(e));
});