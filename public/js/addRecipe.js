let addFieldsBtn = document.getElementById('addFieldsBtn');

let foodCol = document.getElementById('foodCol');
let quantityCol = document.getElementById('quantityCol');
let unitCol = document.getElementById('unitCol');

let foodSelect1 = document.getElementById('food1');
let foods = [];
for (let i = 0; i < foodSelect1.options.length; i++) {
	foods.push(foodSelect1.options[i].childNodes[0].textContent);
}

let unitSelector1 = document.getElementById('unit1');
let units = [];
for (let i = 0; i < unitSelector1.length; i++) {
	units.push(unitSelector1.options[i].childNodes[0].textContent);
}

let fieldsCount = 1;

const addFields = () => {
	
	fieldsCount++;
	// Add food selector (1st col)
	let select = document.createElement('select');
	select.id = "food" + fieldsCount;
	select.name = "food" + fieldsCount;
	select.required = "required"
	for (i = 0; i < foods.length; i++) {
		let option = document.createElement('option');
		option.value = foods[i];
		option.textContent = foods[i];
		select.appendChild(option);
	}
	foodCol.appendChild(select);

	// Add quantity input (2nd col)
	let input = document.createElement('input');
	input.type = "number"
	input.min = "0"
	input.max = "100000"
	input.id = "quantity" + fieldsCount;
	input.name = "quantity" + fieldsCount;
	input.required = "required"
	input.value = 0;
	quantityCol.appendChild(input);

	// Add unit selector (3rd col)
	select = document.createElement('select');
	select.id = "unit" + fieldsCount;
	select.name = "unit" + fieldsCount;
	select.required = "required";
	for (i = 0; i < units.length; i++) {
		let option = document.createElement('option');
		option.value = units[i];
		option.textContent = units[i];
		if (option.textContent == 'g') {
			option.setAttribute('selected', '');
		}
		select.appendChild(option);
	}
	unitCol.appendChild(select);
}

addFieldsBtn.addEventListener('click', addFields);