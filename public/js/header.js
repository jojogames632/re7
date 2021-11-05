let burgerBtn = document.getElementById('burgerBtn');
let closeBtn = document.getElementById('closeBtn');
let mobileNav = document.getElementById('mobileNav');
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
