const recipeTitleInput = document.getElementById('recipeTitle');
const recipesSection = document.getElementById('recipesSection');

const categorySelector = document.getElementById('categorySelector');
const cookingTypeSelector = document.getElementById('cookingTypeSelector');
const typeSelector = document.getElementById('typeSelector');

const Url = new URL(window.location.href);

let category = "";
let cookingType = "";
let type = "";

function fetching(e) {
	fetch(Url.pathname + "?category=" + category + "&cookingType=" + cookingType + "&type=" + type + "&ajax=1", {
		headers: {
			'X-Requested-Width': 'XMLHttpRequest'
		}
	}).then(response => 
		response.json()
	).then(data => {
		recipesSection.innerHTML = data.content;
	}).catch(e => alert(e));
}

categorySelector.addEventListener('change', (e) => {
	category = e.target.value
	fetching(e);	
});

cookingTypeSelector.addEventListener('change', (e) => {
	cookingType = e.target.value
	fetching(e);
});

typeSelector.addEventListener('change', (e) => {
	type = e.target.value
	fetching(e);	
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