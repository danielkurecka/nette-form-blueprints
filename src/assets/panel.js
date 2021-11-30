function getSelectedFormId(root) {
	var result = null;
	root.querySelectorAll('.form-link').forEach(function (el) {
		if (el.classList.contains('form-link-selected')) {
			result = el.id;
		}
	});
	return result;
}

function getSelectedTemplateName(root) {
	var select = root.querySelector('.template-select');
	return select.options[select.selectedIndex].text;
}

function copyToClipboard(root, element) {
	var textArea = document.createElement('textarea');
	textArea.style.position = 'fixed';
	textArea.value = element.textContent;
	root.appendChild(textArea);
	textArea.select();
	var result = true;
	try {
		document.execCommand('copy');
	} catch (err) {
		result = false;
	}
	root.removeChild(textArea);

	if (result) {
		Array.from(shadow.querySelectorAll('.tooltip-copied')).forEach(function (tooltip) {
			tooltip.hidden = false;
			window.setTimeout(function () {
				tooltip.hidden = true;
			}, 650);
		});
	}
	return result;
}

function isolateToShadow(templateElement, wrapWithBody = false) {
	var host = document.createElement('div');
	host.style.all = 'initial';
	templateElement.before(host);
	var shadow = host.attachShadow({'mode': 'open'});

	if (wrapWithBody) {
		var html = document.createElement('html');
		var body = document.createElement('body');
		html.append(body);
		body.append(templateElement.content.cloneNode(true));
		shadow.append(html);
	} else {
		shadow.append(templateElement.content.cloneNode(true));
	}

	return shadow;
}

function prismHighlight(target) {
	if (window.Prism) {
		window.Prism.highlightAllUnder(target)
	}
}

function reloadCurrentForm(root, templateOptions = {}) {
	var params = {
		formId: getSelectedFormId(root),
		templateName: getSelectedTemplateName(root),
		options: templateOptions,
		renderPreview: root.querySelector('.tab-selected').dataset.tabName === 'preview',
	};

	var tracyRefreshOld = window.TracyAutoRefresh;
	window.TracyAutoRefresh = false;
	if (window.Tracy.Debug && window.Tracy.Debug.setOptions) {
		window.Tracy.Debug.setOptions({autoRefresh: false})
	}

	var spinner = root.querySelector('.spinner');
	spinner.hidden = false;
	window.fetch(window.location.href, {headers: {'X-Daku-Nette-Form-Blueprints-Ajax': JSON.stringify(params)}}).then(function (response) {
		spinner.hidden = true;
		return response.text();

	}).then(function (text) {
		var lines = text.split('\n');
		var data = JSON.parse(lines[lines.length - 1]);

		if (data['error']) {
			console.log('Ajax error:', data['error']);

		} else {
			root.querySelector('.template-options').innerHTML = data['templateOptions'];
			root.querySelector('.html-editor-link').setAttribute('href', data['blueprintFileEditorUri']);
			root.querySelector('.detail-latte pre code').innerHTML = data['latte'];
			root.querySelector('.select-range-list').innerHTML = data['selectRangeListHtml'];
			root.querySelector('.detail-preview div').shadowRoot.querySelector('body').innerHTML = data['preview'];
			root.querySelector('.detail-css pre code').textContent = data['styles'];
			addCommonListeners(root);
			prismHighlight(root);
			updateAutoResizable(root, panel);
		}
	});

	window.TracyAutoRefresh = tracyRefreshOld;
	if (window.Tracy.Debug && window.Tracy.Debug.setOptions) {
		window.Tracy.Debug.setOptions({autoRefresh: tracyRefreshOld})
	}
}

function updateAutoResizable(root, panel) {
	var panelRect = panel.getBoundingClientRect();
	requestAnimationFrame(function () {
		root.querySelectorAll('.auto-resizable').forEach(function (el) {
			if (el.offsetParent) {
				var rect = el.getBoundingClientRect();
				el.style.maxWidth = (panelRect.width - (rect.left - panelRect.left) - 15) + 'px';
				el.style.maxHeight = (panelRect.height - (rect.top - panelRect.top) - 35) + 'px';
			}
		});
	});
}

function addCommonListeners(root) {
	// template options changing
	root.querySelectorAll('.input-option').forEach(function (el) {
		el.addEventListener('change', function (e) {
			var value = el.type == 'checkbox' ? el.checked : el.value;
			reloadCurrentForm(shadow, {[el.name]: value});
		});
	});

	// copy to clipboard buttons
	shadow.querySelectorAll('.copy-button').forEach(function (el) {
		el.addEventListener('click', function (e) {
			e.preventDefault();
			copyToClipboard(shadow, el.closest('.detail').querySelector('pre'));
		});
	});

	// copy individual items
	root.querySelector('.select-range-list').addEventListener('click', function (e) {
		var target = e.target;
		if (target.nodeName === 'A') {
			var rangeElement = root.querySelectorAll('.select-range').item(target.getAttribute('data-index'));
			copyToClipboard(root, rangeElement)
			e.preventDefault();
		}
	});
}

var panel = document.querySelector('#tracy-debug-panel-Daku-Nette-FormBlueprints-BlueprintsPanel');

// put contents of <template> elements to shadow element (for isolated styles)
var shadow = isolateToShadow(panel.querySelector('template'));
isolateToShadow(shadow.querySelector('.detail-preview template'), true);

// form switching
shadow.querySelectorAll('.form-link').forEach(function (el) {
	el.addEventListener('click', function (e) {
		el.closest('.form-link-list').querySelectorAll('.form-link').forEach(el => el.classList.remove('form-link-selected'));
		el.classList.add('form-link-selected');
		reloadCurrentForm(shadow);
	});
});

// template switching
shadow.querySelector('.template-select').addEventListener('change', (e) => reloadCurrentForm(shadow));

addCommonListeners(shadow);

// tab detail switching
shadow.querySelectorAll('.tab').forEach(function (el) {
	el.addEventListener('click', function (e) {
		el.closest('.tab-row').querySelectorAll('.tab').forEach(el => el.classList.remove('tab-selected'));
		el.classList.add('tab-selected');
		shadow.querySelectorAll(el.dataset.targetHide).forEach(el => el.hidden = true);
		shadow.querySelectorAll(el.dataset.targetShow).forEach(el => el.hidden = false);
		window.localStorage.setItem('form-blueprints-last-tab-name', el.dataset.tabName);
		if (el.dataset.tabName === 'preview') {
			reloadCurrentForm(shadow);
		}
		updateAutoResizable(shadow, panel);
	});
});

// show last tab
var lastTabName = window.localStorage.getItem('form-blueprints-last-tab-name') || 'latte';
shadow.querySelector('.tab[data-tab-name=' + lastTabName).classList.add('tab-selected');
shadow.querySelector('.detail-' + lastTabName).hidden = false;
if (lastTabName === 'preview') {
	reloadCurrentForm(shadow);
}

// auto resize according to panel
new ResizeObserver(() => updateAutoResizable(shadow, panel)).observe(panel);

// syntax highlighting using Prism
var script = document.createElement('script');
script.src = 'https://cdn.jsdelivr.net/combine/npm/prismjs@1.19.0,npm/prismjs@1.19.0/components/prism-markup-templating.min.js,npm/prismjs@1.19.0/components/prism-php.min.js,npm/prismjs@1.19.0/components/prism-latte.min.js,npm/prismjs@1.19.0/plugins/keep-markup/prism-keep-markup.min.js';
script.dataset.manual = '';
script.onload = () => prismHighlight(shadow);
shadow.append(script);
