let addToPlanningBtns = document.querySelectorAll('.addToPlanningBtn');
let addToPlanningSection = document.getElementById('addToPlanningSection');
let recipeNameElement = document.getElementById('recipeName');

const addToPlanning = (recipeName) => {
	addToPlanningSection.style.display = 'block';
	recipeNameElement.value = recipeName;
}
