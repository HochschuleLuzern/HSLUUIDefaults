il = il || {};
il.hslu = il.hslu || {};

il.hslu = (() => {	
	let checkAllCheckboxes = (t) => {
		let inputs = document.getElementById("ilToolbar").getElementsByTagName("input");
		for (let i = 0; i < inputs.length; i++) {
			if (inputs[i].type === 'checkbox') {
				inputs[i].checked = t.checked;
			}	
		}
	}
	
	let initializeCheckboxes = () => {
		let checkbox = "<table class='table table-striped fullwidth'><thead></thead><tbody><tr><td><input class='selectall' type='checkBox' onclick='il.hslu.checkAllCheckboxes(this)' /><span style='margin-left: 10px'>Select All</span></td></tr></tbody></table>";
		let toolbar = document.getElementsByClassName("ilToolbar");
		toolbar[0].insertAdjacentHTML('beforeend', checkbox);
		toolbar[toolbar.length - 1].insertAdjacentHTML('afterbegin', checkbox);
	}
	
	return {
		checkAllCheckboxes: checkAllCheckboxes,
		initializeCheckboxes: initializeCheckboxes
	}
})();