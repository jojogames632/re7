let burgerBtn = document.querySelector('.burgerBtn');
let closeBtn = document.querySelector('.closeBtn');
let mobileNav = document.querySelector('.mobileNav');
let isMobileOn = false;

const toggleMobileNav = () => {
	if (!isMobileOn) {
		mobileNav.style.display = 'block';
	}
	else {
		mobileNav.style.display = 'none';
	}
	isMobileOn = !isMobileOn;
}

burgerBtn.addEventListener('click', toggleMobileNav);
closeBtn.addEventListener('click', toggleMobileNav);
