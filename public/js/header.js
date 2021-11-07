let burgerBtn = document.getElementById('burgerBtn');
let closeBtn = document.getElementById('closeBtn');
let mobileNav = document.getElementById('mobileNav');
let isMobileOn = false;

// Delete success message for adding a recipe
let alertContainer = document.querySelector('#alertContainer');

const toggleMobileNav = () => {
	if (isMobileOn) {
		mobileNav.style.display = 'none';
	}
	else {
		mobileNav.style.display = 'block';
		if (alertContainer !== null) {
			alertContainer.remove();
		}
	}
	isMobileOn = isMobileOn ? false : true;
}

burgerBtn.addEventListener('click', toggleMobileNav);
closeBtn.addEventListener('click', toggleMobileNav);
