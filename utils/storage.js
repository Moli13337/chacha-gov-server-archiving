export function isObject(val) {
	return val !== null && typeof val === 'object';
}

export function setItem(key, val) {
	if (isObject(val)) {
		localStorage.setItem(key, JSON.stringify(val));
	} else {
		localStorage.setItem(key, val);
	}
}

export function getItem(key) {
	var val = localStorage.getItem(key);

	try {
		return JSON.parse(val);
	} catch (e) {
		return val;
	}
}

export function removeItem(key) {
	localStorage.removeItem(key);
}

export function clear() {
	localStorage.clear();
}

export default {
	isObject,
	setItem,
	getItem,
	removeItem,
	clear
};
