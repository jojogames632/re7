const recipeTitleInput = document.getElementById('recipeTitle');
const recipesSection = document.getElementById('recipesSection');
const categorySelector = document.getElementById('categorySelector');
const Url = new URL(window.location.href);

categorySelector.addEventListener('change', (e) => {
	fetch(Url.pathname + "?category=" + e.target.value + "&ajax=1", {
		headers: {
			'X-Requested-Width': 'XMLHttpRequest'
		}
	}).then(response => 
		response.json()
	).then(data => {
		recipesSection.innerHTML = data.content;
	}).catch(e => alert(e));	
});

recipeTitleInput.addEventListener('input', (e) => { 
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